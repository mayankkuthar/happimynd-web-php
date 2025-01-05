<?php

namespace App\Services;

use Mail;
use Exception;
use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Token;
use App\Models\Package;
use App\Mail\TokenEmail;
use App\Models\TokenPlan;
use App\Models\ThriveCode;
use App\Jobs\TokenEmailJob;
use App\Models\BundleStatus;
use App\Models\DataGroup;
use App\Models\Organization;
use App\Models\UserToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelReader;

class TokenService
{

    /**
     * generats unique tokens
     *
     * @param integer $count
     * @return array
     */
    function generateToken(int $count = 1, $plans, $use_limit=null, $saveDB = false)
    {
        $tokens = [];
        if(empty($use_limit)){
            $use_limit=1;
        }
        for ($i = 1; $i <= $count; $i++) {
            $tokens[] = [
                "token" => substr(md5(uniqid()), 0, 6),
                "use_limit" => $use_limit,
                "use_count" => 0
            ];
        }
        if ($saveDB) {
            Token::insert($tokens);
        }
        return $tokens;
    }

    
    public function generateTokenForOrganization($organization_id, $count = 1, $plans, $use_limit=null, $emails = [], $attachmentPath = '', $emailBody = '')
    {

        $organization = Organization::find($organization_id);
        $tokens = $this->generateToken($count, $plans, $use_limit);
        // if emails were added via excel file
        if ($emails) {
            $tokens = $this->mapEmailTokenSendEmail($emails, $tokens, $plans, $organization, $attachmentPath, $emailBody);
        }
        $planss = [];
        $tokens = $organization->token()->createMany($tokens);
        $now = Carbon::now()->toDateTimeString();
        foreach ($tokens as $token) {
            foreach ($plans as $plan) {
                $planss[] = ['plan_id' => $plan, 'token_id' => $token->id, 'created_at' => $now];
            }
        }
        TokenPlan::insert($planss);
        return $tokens;
    }

    function verifyToken($token, $organization_id)
    {
        if (is_null($token)) {
            return false;
        }
        $this->token = Token::where('token', $token)->where('organization_id', $organization_id)->first();
        $this->setStatus();
        if (is_null($this->token)) {
            return false;
        }
        if (!$this->token->isUsable()) {
            return false;
        }
        if ($this->token->isMaxUsed()) {
            return false;
        }
        return true;
    }

    function assignToken($token, $user_id)
    {
        //Token::where('token', $token)->update(['user_id' => $user_id, 'expired_at' => Carbon::now()]);
        $token = Token::with('plans')->where('token', $token)->first();
        $use_count = $token->use_count;
        $use_count += 1;
        Token::where('token', $token->token)->update(['use_count' => $use_count]);
        UserToken::create(['user_id' => $user_id, 'token_id' => $token->id]);
        $user = User::find($user_id);
        $bundleStatuses = array();
        $paymentDetails = [];
        foreach ($token->plans as $tokenplan) {
            if ($tokenplan->plan->package->name == 'HappiLIFE Screening') {
                $bundlestatus = BundleStatus::create(['user_id' => $user_id, 'plan_id' => $tokenplan->plan_id]);
                $tokenplan->bundle_status_id = $bundlestatus->id;
                $tokenplan->save();
                array_push($bundleStatuses, array(
                    "package_id" => $bundlestatus->plans->package_id,
                    "price" => 0,
                    "quantity" => 1,
                ));
                // prepare data for bitrix
                if ($bundlestatus->plans->package->name == "HappiLIFE Screening") {
                    $paymentDetails +=
                        [
                            'happiLIFEScreening' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                } else if ($bundlestatus->plans->package->name == "HappiLIFE Summary Reading") {
                    $paymentDetails +=
                        [
                            'happiLIFESummaryReading' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                } else if ($bundlestatus->plans->package->name == "HappiAPP") {
                    $paymentDetails +=
                        [
                            'happiAPP' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                } else if ($bundlestatus->plans->package->name == "HappiCHAT") {
                    $paymentDetails +=
                        [
                            'happiCHAT' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                } else if ($bundlestatus->plans->package->name == "HappiTALK") {
                    $paymentDetails +=
                        [
                            'happiTALK' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                } else if ($bundlestatus->plans->package->name == "HappiCHAT + HappiAPP") {
                    $paymentDetails +=
                        [
                            'happiCHAThappiAPP' => [
                                "paymentOrderLink" => 'Not Required',
                                'paymentStatus' => "Added",
                                "makePaymentLink" => "Link not required added while signup",
                            ]

                        ];
                }
            }
        }
        $user->addProductDealToBitrix($bundleStatuses);
        $user->updateBitrixDeal(array("paymentDetails" => $paymentDetails));
        return $token;
    }

    protected function setStatus()
    {
        if (!is_null($this->token)) {
            if ($this->token->isExpired()) {
                $this->status = 'Code is Expired.';
            }
            if ($this->token->isMaxUsed()) {
                $this->status = 'Max user limit for this code is over.';
            }
            if ($this->token->isDisabled()) {
                $this->status = 'Sorry, Code has been revoked.';
            }
        } else {
            $this->status = 'Invalid Code.';
        }
    }

    function assignThriveCode($thriveCodeId, $user_id)
    {
        $status = ThriveCode::where('id', $thriveCodeId)->update(['user_id' => $user_id, 'expired_at' => Carbon::now()]);
        return $status;
    }

    private function readFromFile($file, $organization)
    {
        $data = array();
        $filePath = \Storage::disk('public')->put($file->getClientOriginalName(), $file);
        $filePath = storage_path('app/public') . '/' . $filePath;
        $rows = SimpleExcelReader::create($filePath)->getRows();
        foreach ($rows as $row) {
            array_push($data, array("code" => $row['Thrive Codes']));
        }
        return $data;
    }

    public function generateThriveTokenForOrganization($organizationId, $file)
    {
        $organization = Organization::find($organizationId);
        if ($organization) {
            $thriveCodes = $this->readFromFile($file, $organization);
            $thriveCodes = $organization->thriveCode()->createMany($thriveCodes);
            return $thriveCodes;
        }
        return false;
    }

    public function getEmailsFromExcel($file)
    {
        //get all emails from excel into an array
        $data = array();
        $filePath = \Storage::disk('public')->put($file->getClientOriginalName(), $file);
        $filePath = storage_path('app/public') . '/' . $filePath;
        $rows = SimpleExcelReader::create($filePath)->getRows();
        foreach ($rows as $row) {
            $userRecord = [
                'email' => $row['email_address'],
                'name' => $row['name'],
            ];
            array_push($data, $userRecord);
        }
        return $data;
    }

    public function mapEmailTokenSendEmail($emails, $tokens, $planIds, $organization, $attachmentPath, $emailBody)
    {
        $companyName = ucwords($organization->name);
        $records = 0;
        // check the records for emails and tokens to avoid index offset in loop
        if (count($tokens) > count($emails)) {
            $records = count($emails);
        } elseif (count($tokens) < count($emails)) {
            $records = count($tokens);
        } else {
            $records = count($tokens);
        }
        //loop through every token and map email to save in db
        for ($j = 0; $j < $records; $j++) {
            $tokens[$j]['email'] = $emails[$j]['email'];
            $tokens[$j]['name'] = $emails[$j]['name'];
        }
        $result = json_encode($tokens);
        Log::info($result);
        //dispatch email Job
        $emailSubject = DataGroup::where('name', 'email-orientation')->with(['content' => function ($query) {
            $query->where('title', 'subject');
        }])->first()->content[0]->content;
        dispatch(new TokenEmailJob($tokens, $planIds, $companyName, $attachmentPath, $emailBody, $emailSubject))->delay(1);
        return $tokens;
    }
}
