<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Invoice Platform clients (API_SPEC §3, endpoints 5–6).
 *
 * GET /clients — paged list with latest session/payment and processing status.
 * GET /clients/{id} — one client with sessions, payments, invoices, processing
 *                     and audit history.
 */
class ClientController extends InvoiceApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($denied = $this->requireRole($request, 'VIEWER')) {
            return $denied;
        }

        [$page, $pageSize, $offset] = $this->paging($request);

        $sortBy = (string) $request->query('sortBy', 'updatedAt');
        $sortDir = strtolower((string) $request->query('sortDir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if (!in_array($sortBy, ['clientName', 'createdAt', 'updatedAt'], true)) {
            $sortBy = 'updatedAt';
        }

        $search = $request->query('search');
        $state = $request->query('state');
        $product = $request->query('product');
        $paymentMode = $request->query('paymentMode');
        $includeInactive = filter_var($request->query('includeInactive', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = $this->db()->table('Client as c')->select('c.*');

        if (!$includeInactive) {
            $query->where('c.active', 1);
        }

        if (is_string($state) && $state !== '') {
            $query->where('c.state', $state);
        }

        if (is_string($search) && $search !== '') {
            $like = '%' . $this->escapeLike($search) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('c.clientName', 'like', $like)
                    ->orWhere('c.phone', 'like', $like);
            });
        }

        if (is_string($product) && $product !== '') {
            $query->whereExists(function ($q) use ($product) {
                $q->selectRaw('1')->from('Session as s')
                    ->whereColumn('s.clientId', 'c.id')
                    ->where('s.product', $product);
            });
        }

        if (is_string($paymentMode) && $paymentMode !== '') {
            $query->whereExists(function ($q) use ($paymentMode) {
                $q->selectRaw('1')->from('Payment as p')
                    ->whereColumn('p.clientId', 'c.id')
                    ->where('p.paymentMode', $paymentMode);
            });
        }

        $total = (clone $query)->count();

        $rows = $query->orderBy('c.' . $sortBy, $sortDir)
            ->orderBy('c.id', 'asc')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        $clients = $this->decorate($rows);

        return $this->ok($this->paged($clients, $page, $pageSize, (int) $total));
    }

    public function show(string $id): JsonResponse
    {
        $client = $this->db()->table('Client')->where('id', $id)->first();
        if ($client === null) {
            return $this->error('Client not found', 404);
        }

        $sessions = $this->db()->table('Session')
            ->where('clientId', $id)
            ->orderBy('sessionNumber')
            ->get();

        $sessionIds = array_map(function ($s) {
            return $s->id;
        }, $sessions->all());

        $payments = $sessionIds === [] ? collect() : $this->db()->table('Payment')
            ->whereIn('sessionId', $sessionIds)
            ->orderBy('paymentDate')
            ->get();

        $voice = $sessionIds === [] ? collect() : $this->db()->table('VoiceProcessing')
            ->whereIn('sessionId', $sessionIds)
            ->orderBy('createdAt')
            ->get();

        $data = $sessionIds === [] ? collect() : $this->db()->table('DataProcessing')
            ->whereIn('sessionId', $sessionIds)
            ->orderBy('createdAt')
            ->get();

        $invoices = $this->db()->table('InvoiceTransaction')
            ->where('clientId', $id)
            ->orderBy('paymentDate')
            ->get();

        $invoiceById = [];
        $invoiceByNumber = [];
        foreach ($invoices as $inv) {
            $invoiceById[$inv->id] = $inv;
            if ($inv->invoiceNumber !== null && $inv->invoiceNumber !== '') {
                $invoiceByNumber[$inv->invoiceNumber] = $inv;
            }
        }

        $templates = $this->db()->table('Template')->get()->keyBy('id');

        $paymentsBySession = $payments->groupBy('sessionId');
        $voiceBySession = $voice->groupBy('sessionId');
        $dataBySession = $data->groupBy('sessionId');
        $invoicesByData = $invoices->keyBy('dataProcessingId');

        $sessionPayload = [];
        foreach ($sessions as $s) {
            $pays = $paymentsBySession->get($s->id, collect())->map(function ($p) use ($invoiceById, $invoiceByNumber) {
                $linked = [];
                if ($p->invoiceTransactionId !== null && isset($invoiceById[$p->invoiceTransactionId])) {
                    $linked[] = $this->presentInvoice($invoiceById[$p->invoiceTransactionId]);
                } elseif ($p->invoiceNumber !== null && isset($invoiceByNumber[$p->invoiceNumber])) {
                    $linked[] = $this->presentInvoice($invoiceByNumber[$p->invoiceNumber]);
                }

                return array_merge($this->presentPayment($p), ['invoiceTransactions' => $linked]);
            })->values()->all();

            $sessionPayload[] = array_merge(
                $this->presentSession($s),
                [
                    'payments' => $pays,
                    'voiceProcessing' => $voiceBySession->get($s->id, collect())->map(function ($v) use ($templates) {
                        return array_merge(
                            $this->presentVoice($v),
                            ['template' => $v->templateId !== null && $templates->has($v->templateId)
                                ? $this->presentTemplate($templates->get($v->templateId))
                                : null]
                        );
                    })->values()->all(),
                    'dataProcessing' => $dataBySession->get($s->id, collect())->map(function ($d) use ($templates, $invoicesByData) {
                        return array_merge(
                            $this->presentData($d),
                            ['template' => $d->templateId !== null && $templates->has($d->templateId)
                                ? $this->presentTemplate($templates->get($d->templateId))
                                : null]
                        );
                    })->values()->all(),
                ]
            );
        }

        $paymentIds = $payments->pluck('id')->all();
        $auditRows = $this->auditFor($id, $sessionIds, $paymentIds);

        return $this->ok([
            'client' => array_merge($this->presentClient($client), [
                'sessions' => $sessionPayload,
            ]),
            'auditLogs' => $auditRows,
        ]);
    }

    /** Correlate a page of clients with their aggregates and latest rows. */
    private function decorate($rows): array
    {
        $ids = $rows->pluck('id')->all();
        if ($ids === []) {
            return [];
        }

        $sessionAgg = $this->db()->table('Session')
            ->whereIn('clientId', $ids)
            ->groupBy('clientId')
            ->selectRaw('clientId, COUNT(*) AS cnt, '
                . 'COALESCE(SUM(COALESCE(`sessionsConducted`,0)),0) AS completed, '
                . 'COALESCE(SUM(COALESCE(`sessionsPending`,0)),0) AS pending')
            ->get()
            ->keyBy('clientId');

        $latestSession = $this->windowOne('Session', $ids, ['product', 'sessionStatus']);
        $latestPayment = $this->windowOne('Payment', $ids, [
            'paymentAmount', 'paymentDate', 'paymentMode', 'invoiceNumber',
            'paymentVerified', 'invoiceTransactionId', 'status',
        ]);
        $latestVoice = $this->windowOne('VoiceProcessing', $ids, ['status']);
        $latestData = $this->windowOne('DataProcessing', $ids, ['status']);

        // Invoice status for each client's latest payment.
        $invoiceNumbers = $latestPayment->pluck('invoiceNumber')->filter()->unique()->values()->all();
        $invoiceIds = $latestPayment->pluck('invoiceTransactionId')->filter()->unique()->values()->all();

        $invoiceStatusById = [];
        $invoiceStatusByNumber = [];
        if ($invoiceIds !== [] || $invoiceNumbers !== []) {
            $byId = $invoiceIds !== []
                ? $this->db()->table('InvoiceTransaction')->whereIn('id', $invoiceIds)->get()
                : collect();
            $byNumber = $invoiceNumbers !== []
                ? $this->db()->table('InvoiceTransaction')->whereIn('invoiceNumber', $invoiceNumbers)->get()
                : collect();

            foreach ($byId as $inv) {
                $invoiceStatusById[$inv->id] = (string) $inv->status;
            }
            foreach ($byNumber as $inv) {
                $invoiceStatusByNumber[$inv->invoiceNumber] = (string) $inv->status;
            }
        }

        $out = [];
        foreach ($rows as $c) {
            $agg = $sessionAgg->get($c->id);
            $completed = $agg ? (int) $agg->completed : 0;
            $pending = $agg ? (int) $agg->pending : 0;

            $ls = $latestSession->get($c->id);
            $lp = $latestPayment->get($c->id);
            $lv = $latestVoice->get($c->id);
            $ld = $latestData->get($c->id);

            $invoiceStatus = null;
            if ($lp !== null) {
                if ($lp->invoiceTransactionId !== null && isset($invoiceStatusById[$lp->invoiceTransactionId])) {
                    $invoiceStatus = $invoiceStatusById[$lp->invoiceTransactionId];
                } elseif ($lp->invoiceNumber !== null && isset($invoiceStatusByNumber[$lp->invoiceNumber])) {
                    $invoiceStatus = $invoiceStatusByNumber[$lp->invoiceNumber];
                }
            }

            $out[] = [
                'id' => (string) $c->id,
                'clientName' => (string) $c->clientName,
                'phone' => $c->phone !== null ? (string) $c->phone : null,
                'state' => $c->state !== null ? (string) $c->state : null,
                'profile' => $c->profile !== null ? (string) $c->profile : null,
                'product' => $ls && $ls->product !== null ? (string) $ls->product : null,
                'sessionCount' => $agg ? (int) $agg->cnt : 0,
                'totalSessions' => $completed + $pending,
                'completedSessions' => $completed,
                'pendingSessions' => $pending,
                'paymentAmount' => $lp ? $this->money($lp->paymentAmount) : null,
                'paymentDate' => $lp ? $this->iso($lp->paymentDate) : null,
                'paymentMode' => $lp && $lp->paymentMode !== null ? (string) $lp->paymentMode : null,
                'invoiceNumber' => $lp && $lp->invoiceNumber !== null ? (string) $lp->invoiceNumber : null,
                'paymentVerified' => $lp ? $this->asBool($lp->paymentVerified) : false,
                'invoiceStatus' => $invoiceStatus,
                'voiceStatus' => $lv !== null ? (string) $lv->status : null,
                'dataStatus' => $ld !== null ? (string) $ld->status : null,
                'sessionStatus' => $ls !== null ? (string) $ls->sessionStatus : null,
                'active' => $this->asBool($c->active),
                'updatedAt' => $this->iso($c->updatedAt),
            ];
        }

        return $out;
    }

    /** Latest row per client using a window function (MySQL 8). */
    private function windowOne(string $table, array $ids, array $columns)
    {
        $sql = 'SELECT * FROM (SELECT *, '
            . 'ROW_NUMBER() OVER (PARTITION BY `clientId` ORDER BY `updatedAt` DESC, `id` DESC) AS rn '
            . 'FROM `' . $table . '` WHERE `clientId` IN (' . implode(',', array_fill(0, count($ids), '?')) . ')) t '
            . 'WHERE t.rn = 1';

        return collect($this->db()->select($sql, $ids))->keyBy('clientId');
    }

    private function auditFor(string $clientId, array $sessionIds, array $paymentIds)
    {
        $query = $this->db()->table('AuditLog')
            ->where(function ($q) use ($clientId) {
                $q->where(function ($q2) use ($clientId) {
                    $q2->where('entity', 'Client')->where('entityId', $clientId);
                });
            });

        if ($sessionIds !== []) {
            $query->orWhere(function ($q) use ($sessionIds) {
                $q->where('entity', 'Session')->whereIn('entityId', $sessionIds);
            });
        }
        if ($paymentIds !== []) {
            $query->orWhere(function ($q) use ($paymentIds) {
                $q->where('entity', 'Payment')->whereIn('entityId', $paymentIds);
            });
        }

        return $query->orderBy('timestamp', 'desc')->limit(100)->get()
            ->map(function ($r) {
                return $this->presentAudit($r, null);
            })->values()->all();
    }

    protected function presentClient($c): array
    {
        return [
            'id' => (string) $c->id,
            'matchKey' => (string) $c->matchKey,
            'clientName' => (string) $c->clientName,
            'age' => $c->age !== null ? (string) $c->age : null,
            'gender' => $c->gender !== null ? (string) $c->gender : null,
            'phone' => $c->phone !== null ? (string) $c->phone : null,
            'phoneNormalized' => $c->phoneNormalized !== null ? (string) $c->phoneNormalized : null,
            'email' => $c->email !== null ? (string) $c->email : null,
            'state' => $c->state !== null ? (string) $c->state : null,
            'profile' => $c->profile !== null ? (string) $c->profile : null,
            'active' => $this->asBool($c->active),
            'createdAt' => $this->iso($c->createdAt),
            'updatedAt' => $this->iso($c->updatedAt),
        ];
    }

    protected function presentSession($s): array
    {
        return [
            'id' => (string) $s->id,
            'clientId' => (string) $s->clientId,
            'product' => $s->product !== null ? (string) $s->product : null,
            'source' => $s->source !== null ? (string) $s->source : null,
            'profile' => $s->profile !== null ? (string) $s->profile : null,
            'packageDescription' => $s->packageDescription !== null ? (string) $s->packageDescription : null,
            'sessionNumber' => (int) $s->sessionNumber,
            'reportedSessionCount' => $s->reportedSessionCount !== null ? (int) $s->reportedSessionCount : null,
            'sessionsConducted' => $s->sessionsConducted !== null ? (int) $s->sessionsConducted : null,
            'sessionsPending' => $s->sessionsPending !== null ? (int) $s->sessionsPending : null,
            'sessionDate' => $this->iso($s->sessionDate),
            'sessionStatus' => (string) $s->sessionStatus,
            'active' => $this->asBool($s->active),
            'externalRowId' => $s->externalRowId !== null ? (string) $s->externalRowId : null,
            'sourceHash' => $s->sourceHash !== null ? (string) $s->sourceHash : null,
            'lastSyncedAt' => $this->iso($s->lastSyncedAt),
            'createdAt' => $this->iso($s->createdAt),
            'updatedAt' => $this->iso($s->updatedAt),
        ];
    }

    protected function presentPayment($p): array
    {
        return [
            'id' => (string) $p->id,
            'clientId' => (string) $p->clientId,
            'sessionId' => $p->sessionId !== null ? (string) $p->sessionId : null,
            'revenueMonth' => $p->revenueMonth !== null ? (string) $p->revenueMonth : null,
            'paymentMode' => $p->paymentMode !== null ? (string) $p->paymentMode : null,
            'paymentDate' => $this->iso($p->paymentDate),
            'paymentAmount' => $this->money($p->paymentAmount),
            'taxableValue' => $this->money($p->taxableValue),
            'igst' => $this->money($p->igst),
            'cgst' => $this->money($p->cgst),
            'sgst' => $this->money($p->sgst),
            'invoiceNumber' => $p->invoiceNumber !== null ? (string) $p->invoiceNumber : null,
            'paymentProof' => $p->paymentProof !== null ? (string) $p->paymentProof : null,
            'paymentProofType' => (string) $p->paymentProofType,
            'paymentStatus' => $p->paymentStatus !== null ? (string) $p->paymentStatus : null,
            'paymentVerified' => $this->asBool($p->paymentVerified),
            'invoiceTransactionId' => $p->invoiceTransactionId !== null ? (string) $p->invoiceTransactionId : null,
            'createdAt' => $this->iso($p->createdAt),
            'updatedAt' => $this->iso($p->updatedAt),
        ];
    }

    protected function presentVoice($v): array
    {
        return [
            'id' => (string) $v->id,
            'clientId' => (string) $v->clientId,
            'sessionId' => (string) $v->sessionId,
            'templateId' => $v->templateId !== null ? (string) $v->templateId : null,
            'status' => (string) $v->status,
            'contentVersion' => (int) $v->contentVersion,
            'idempotencyKey' => (string) $v->idempotencyKey,
            'retryCount' => (int) $v->retryCount,
            'error' => $v->error !== null ? (string) $v->error : null,
            'processedAt' => $this->iso($v->processedAt),
            'createdAt' => $this->iso($v->createdAt),
            'updatedAt' => $this->iso($v->updatedAt),
        ];
    }

    protected function presentData($d): array
    {
        return [
            'id' => (string) $d->id,
            'clientId' => (string) $d->clientId,
            'sessionId' => (string) $d->sessionId,
            'templateId' => $d->templateId !== null ? (string) $d->templateId : null,
            'status' => (string) $d->status,
            'contentVersion' => (int) $d->contentVersion,
            'idempotencyKey' => (string) $d->idempotencyKey,
            'retryCount' => (int) $d->retryCount,
            'error' => $d->error !== null ? (string) $d->error : null,
            'processedAt' => $this->iso($d->processedAt),
            'lastEmailedAt' => $this->iso($d->lastEmailedAt),
            'lastEmailedTo' => $d->lastEmailedTo !== null ? (string) $d->lastEmailedTo : null,
            'createdAt' => $this->iso($d->createdAt),
            'updatedAt' => $this->iso($d->updatedAt),
        ];
    }

    protected function presentInvoice($i): array
    {
        $flags = $this->json($i->flags, []);

        return [
            'id' => (string) $i->id,
            'transactionKey' => (string) $i->transactionKey,
            'clientId' => (string) $i->clientId,
            'dataProcessingId' => $i->dataProcessingId !== null ? (string) $i->dataProcessingId : null,
            'clientNameRaw' => (string) $i->clientNameRaw,
            'invoiceNumber' => $i->invoiceNumber !== null ? (string) $i->invoiceNumber : null,
            'paymentDate' => $this->iso($i->paymentDate),
            'paymentStatusRaw' => $i->paymentStatusRaw !== null ? (string) $i->paymentStatusRaw : null,
            'paymentVerified' => $this->asBool($i->paymentVerified),
            'paymentAmount' => $this->money($i->paymentAmount),
            'taxableValue' => $this->money($i->taxableValue),
            'igst' => $this->money($i->igst),
            'cgst' => $this->money($i->cgst),
            'sgst' => $this->money($i->sgst),
            'paymentMode' => $i->paymentMode !== null ? (string) $i->paymentMode : null,
            'paymentProof' => $i->paymentProof !== null ? (string) $i->paymentProof : null,
            'sessionsBooked' => $i->sessionsBooked !== null ? (int) $i->sessionsBooked : null,
            'paymentsCount' => (int) $i->paymentsCount,
            'status' => (string) $i->status,
            'reviewReason' => $i->reviewReason !== null ? (string) $i->reviewReason : null,
            'flags' => is_array($flags) ? array_values($flags) : [],
            'occurrenceCount' => (int) $i->occurrenceCount,
            'generatedAt' => $this->iso($i->generatedAt),
            'error' => $i->error !== null ? (string) $i->error : null,
            'createdAt' => $this->iso($i->createdAt),
            'updatedAt' => $this->iso($i->updatedAt),
        ];
    }

    protected function presentTemplate($t): array
    {
        return [
            'id' => (string) $t->id,
            'name' => (string) $t->name,
            'type' => (string) $t->type,
            'body' => (string) $t->body,
            'dependencyFields' => is_array($dep = $this->json($t->dependencyFields, [])) ? array_values($dep) : [],
            'active' => $this->asBool($t->active),
            'version' => (int) $t->version,
            'createdAt' => $this->iso($t->createdAt),
            'updatedAt' => $this->iso($t->updatedAt),
        ];
    }

    protected function presentAudit($r, $user): array
    {
        $out = [
            'id' => (string) $r->id,
            'actorType' => (string) $r->actorType,
            'userId' => $r->userId !== null ? (string) $r->userId : null,
            'action' => (string) $r->action,
            'entity' => (string) $r->entity,
            'entityId' => (string) $r->entityId,
            'oldValue' => $this->json($r->oldValue),
            'newValue' => $this->json($r->newValue),
            'timestamp' => $this->iso($r->timestamp),
        ];

        if ($user !== null) {
            $out['user'] = $user;
        }

        return $out;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
