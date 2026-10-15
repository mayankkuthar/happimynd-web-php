<?php

namespace App\Http\Controllers\Invoice\Concerns;

/**
 * Row presenters for the Invoice Platform API: turn a database row into the
 * JSON shape the front end expects (ISO dates, numbers, booleans).
 *
 * Requires the host class to extend InvoiceApiController (iso/money/asBool/json).
 */
trait PresentsRows
{
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

    protected function presentAudit($r, $user = null): array
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
            'user' => $user,
        ];

        return $out;
    }
}
