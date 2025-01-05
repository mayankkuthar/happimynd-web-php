<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class BitrixService
{
    /**
     *
     */

    public function __construct()
    {
        /** Get the Bitrix webhook. */
        $this->webhook = env('BITRIX_WEBHOOK');

        /** Mapping happimynd user attribute to bitrix lead attributes. */
        $this->leadAttributes = array(
            "username"      => "FIELDS[UF_CRM_1614078775]",
            "nickname"      => "FIELDS[TITLE]",
            "email"         => "FIELDS[EMAIL][0][VALUE]",
            // "profession"    => "FIELDS[UF_CRM_1614352040344]",
            "profileType"   => "FIELDS[UF_CRM_1614078702]",
            "age"           => "FIELDS[UF_CRM_1614078717]",
            "gender"        => "FIELDS[UF_CRM_1614759321]",      // 871 for male, 873 for female, 875 for other
            "mobile"        => "FIELDS[PHONE][0][VALUE]",
            "account_status" => "FIELDS[UF_CRM_1614351936026]",   // "0" | "1"
            "lead_type"     => "FIELDS[UF_CRM_1613824923520]",    // 89 for B2B, 91 for B2C
            "individual"    => "FIELDS[UF_CRM_1614431080239]",    // "0" | "1"
            "organization"  => "FIELDS[COMPANY_TITLE]",
        );

        /** Mapping happimynd user buied plans to bitrix deal attributes. */
        $this->dealAttributes = array(
            "deal"          => "FIELDS[TITLE]",
            "dealType"      => "FIELDS[UF_CRM_1614743348]",
            "stage"         => "FIELDS[STAGE_ID]",    // WON, NEW, 3=>product select
            "source"        => "FIELDS[SOURCE_ID]",  // WEB
            "contactId"     => "FIELDS[CONTACT_ID]",
            "leadId"        => "FIELDS[LEAD_ID]",
            "username"      => "FIELDS[UF_CRM_6034E79CEC53E]",
            "nickname"      => "FIELDS[UF_CRM_6034E79BE97DB]",
            "profileType"   => "FIELDS[UF_CRM_6034E79CB91A8]",
            "age"           => "FIELDS[UF_CRM_6034E79CD2AD7]",
            "gender"        => "FIELDS[UF_CRM_1614759536]",
            "paymentStatus" => "FIELDS[UF_CRM_1614684908953]",
            "paymentLink"   => "FIELDS[UF_CRM_1614080598811]",
            "makePayment"   => "FIELDS[UF_CRM_1616692283198]",

            "happiLIFEScreeningMakePaymentLink" => "FIELDS[UF_CRM_1623822104978]",
            "happiLIFEScreeningPaymentOrderLink" => "FIELDS[UF_CRM_1623821665143]",
            "happiLIFEScreeningPaymentStatus" => "FIELDS[UF_CRM_1623822452128]",

            "happiLIFESummaryReadingMakePaymentLink" => "FIELDS[UF_CRM_1623822634560]",
            "happiLIFESummaryReadingPaymentOrderLink" => "FIELDS[UF_CRM_1623822657127]",
            "happiLIFESummaryReadingPaymentStatus" => "FIELDS[UF_CRM_1623822952752]",

            "happiCHATMakePaymentLink" => "FIELDS[UF_CRM_1623823032385]",
            "happiCHATPaymentOrderLink" => "FIELDS[UF_CRM_1623823047628]",
            "happiCHATPaymentStatus" => "FIELDS[UF_CRM_1623823133769]",

            "happiCHAThappiAPPMakePaymentLink" => "FIELDS[UF_CRM_1623823186166]",
            "happiCHAThappiAPPPaymentOrderLink" => "FIELDS[UF_CRM_1623824509709]",
            "happiCHAThappiAPPPaymentStatus" => "FIELDS[UF_CRM_1623824628736]",

            "happiAPPMakePaymentLink" => "FIELDS[UF_CRM_1623824646165]",
            "happiAPPPaymentOrderLink" => "FIELDS[UF_CRM_1623824668055]",
            "happiAPPPaymentStatus" => "FIELDS[UF_CRM_1623824729427]",

            "happiTALKMakePaymentLink" => "FIELDS[UF_CRM_1623824797980]",
            "happiTALKPaymentOrderLink" => "FIELDS[UF_CRM_1623824810587]",
            "happiTALKPaymentStatus" => "FIELDS[UF_CRM_1623824862872]",


            "currency"      => "FIELDS[CURRENCY_ID]",
            "amount"        => "FIELDS[OPPORTUNITY]",
            "dealCategory"  => "FIELDS[CATEGORY_ID]",
            "organization"  => "FIELDS[UF_CRM_1617088365735]",
            /** Assessment detail */
            "reportLink"    => "FIELDS[UF_CRM_1616085254658]",
            "calltime"      => "FIELDS[UF_CRM_1616085552939]",
            "slot"          => "FIELDS[UF_CRM_1616085640254]",
            "detailLink"    => "FIELDS[UF_CRM_1616435802735]",

            /** Web notification(array) */
            "webNotification" => "FIELDS[UF_CRM_1616132865]",

            /** Generate Token related */
            'plans'         => 'FIELDS[UF_CRM_1616428197026]',
            'category'      => 'FIELDS[UF_CRM_1616428245989]',
            'token'         => 'FIELDS[UF_CRM_1616428095385]',
            'thriveCode'    => 'FIELDS[UF_CRM_1616428287531]',


            /** happichat and happitalk */
            'HappiCHAT' => "FIELDS[UF_CRM_1623313651810]",
            'HappiTALK' => "FIELDS[UF_CRM_1623313662276]",
            'HappiTALKSessions' => "FIELDS[UF_CRM_1624357117512]",

            'packageId' => "FIELDS[PRODUCT_ID]",
            'PackagePrice' => "FIELDS[PRICE]",

            /** report reading */

            "reportReadingSlotDate" => "FIELDS[UF_CRM_1625488292680]",
            "reportReadingTimeSlot" => "FIELDS[UF_CRM_1625631694364]",
            "reportReadingCommunicationMode" => "FIELDS[UF_CRM_1626442247597]",

            /** raised query */

            "queryDescription" => "FIELDS[UF_CRM_1624437118951]",
            "queryType" => "FIELDS[UF_CRM_1624700230878]",
            'TotalHappiTalkSessionAppointed' => "FIELDS[UF_CRM_1625049572743]",

            'HappiTalkSessionAvailed' =>  "FIELDS[UF_CRM_1625049617309]",
            'HappiTalkSessionRemaining' =>  "FIELDS[UF_CRM_1625049647210]",
            'Psychologist1BasePrice' => "FIELDS[UF_CRM_1625049807135]",
            'BookingTime' => "FIELDS[UF_CRM_1625488292680]",
            'BookedPsychologistName' => "FIELDS[UF_CRM_1625488341321]",
            'PsychologistSlot' => "FIELDS[UF_CRM_1625631694364] "

        );

        /** Inverse Attributes */
        $this->dealInverseAttributes = array(
            "deal"          => "TITLE",
            "dealType"      => "UF_CRM_1614743348",
            "stage"         => "STAGE_ID",
            "source"        => "SOURCE_ID",
            "contactId"     => "CONTACT_ID",
            "leadId"        => "LEAD_ID",
            "username"      => "UF_CRM_6034E79CEC53E",
            "nickname"      => "UF_CRM_6034E79BE97DB",
            "profileType"   => "UF_CRM_6034E79CB91A8",
            "age"           => "UF_CRM_6034E79CD2AD7",
            "gender"        => "UF_CRM_1614759536",
            "paymentStatus" => "UF_CRM_1614684908953",
            "paymentLink"   => "UF_CRM_1614080598811",
            "makePayment"   => "UF_CRM_1616692283198",

            "happiLIFEScreeningMakePaymentLink" => "UF_CRM_1623822104978",
            "happiLIFEScreeningPaymentOrderLink" => "UF_CRM_1623821665143",
            "happiLIFEScreeningPaymentStatus" => "UF_CRM_1623822452128",

            "happiLIFESummaryReadingMakePaymentLink" => "UF_CRM_1623822634560",
            "happiLIFESummaryReadingPaymentOrderLink" => "UF_CRM_1623822657127",
            "happiLIFESummaryReadingPaymentStatus" => "UF_CRM_1623822952752",

            "happiCHATMakePaymentLink" => "UF_CRM_1623823032385",
            "happiCHATPaymentOrderLink" => "UF_CRM_1623823047628",
            "happiCHATPaymentStatus" => "UF_CRM_1623823133769",

            "happiCHAThappiAPPMakePaymentLink" => "UF_CRM_1623823186166",
            "happiCHAThappiAPPPaymentOrderLink" => "UF_CRM_1623824509709",
            "happiCHAThappiAPPPaymentStatus" => "UF_CRM_1623824628736",

            "happiAPPMakePaymentLink" => "UF_CRM_1623824646165",
            "happiAPPPaymentOrderLink" => "UF_CRM_1623824668055",
            "happiAPPPaymentStatus" => "UF_CRM_1623824729427",

            "happiTALKMakePaymentLink" => "UF_CRM_1623824797980",
            "happiTALKPaymentOrderLink" => "UF_CRM_1623824810587",
            "happiTALKPaymentStatus" => "UF_CRM_1623824862872",

            "currency"      => "CURRENCY_ID",
            "amount"        => "OPPORTUNITY",
            "dealCategory"  => "CATEGORY_ID",
            "organization"  => "UF_CRM_1617088365735",
            /** Assessment detail */
            "reportLink"    => "UF_CRM_1616085254658",
            "calltime"      => "UF_CRM_1616085552939",
            "slot"          => "UF_CRM_1616085640254",
            "detailLink"    => "UF_CRM_1616435802735",

            /** Web notification(array) */
            "webNotification" => "UF_CRM_1616132865",

            /** Generate Token related */
            'plans'         => 'UF_CRM_1616428197026',
            'category'      => 'UF_CRM_1616428245989',
            'token'         => 'UF_CRM_1616428095385',
            'thriveCode'    => 'UF_CRM_1616428287531',

            /** happichat and happitalk */
            'HappiCHAT' => "UF_CRM_1623313651810",
            'HappiTALK' => "UF_CRM_1623313662276",
            'HappiTALKSessions' => 'UF_CRM_1624357117512',

            'reportReadingSlotDate' => 'UF_CRM_1625488292680',
            "reportReadingTimeSlot" => "UF_CRM_1625631694364",
            "reportReadingCommunicationMode" => "UF_CRM_1626442247597",

            /** raised query */

            "queryDescription" => "UF_CRM_1624437118951",
            "queryType" => "UF_CRM_1624700230878",

        );

        /** Mapping Happimynd packages to bitrix products. B2C */
        $this->dealProductAttributes = array(
            "packageId" => "PRODUCT_ID",
            "price"     => "PRICE",
            "quantity"  => "QUANTITY"
        );

        /** Bitrix contacts */
        $this->contactAttributes = array(
            "name"  => "FIELDS[NAME]",
            "mobile" => "FIELDS[PHONE][0][VALUE]",
            "email" => "FIELDS[EMAIL][0][VALUE]",
        );

        /** Deal variables */
        $this->dealVariables = array(
            /**
             * Gender
             * ID: "877", VALUE: "Male"
             * ID: "879", VALUE: "Female"
             * ID: "881", VALUE: "Others"
             */
            "gender" => array(
                "Male"  => "877",
                "Female" => "879",
                "Others" => "881"
            ),
            /**
             * ProfileType
             * ID: "345", VALUE: "Salaried"
             * ID: "347", VALUE: "Self-Employed"
             * ID: "349", VALUE: "Home Maker"
             * ID: "351", VALUE: "Job Seeker"
             * ID: "353", VALUE: "Entrepreneur"
             * ID: "355", VALUE: "Frontline Warrior"
             * ID: "357", VALUE: "Senior Citizen"
             * ID: "359", VALUE: "Student (School)"
             * ID: "361", VALUE: "Student (college/University)"
             */
            "profileType" => array(
                "Salaried"      => "345",
                "Self-Employed" => "347",
                "Home Maker"    => "349",
                "Job Seeker"    => "351",
                "Entrepreneur"  => "353",
                "Frontline Warrior" => "355",
                "Senior Citizen"    => "357",
                "Student (School)"  => "359",
                "Student (college/University)" => "361",
                "Working Women"    => "26774"
            ),
            /**
             * paymentStatus
             * ID: "857", VALUE: "Completed"
             * ID: "859", VALUE: "Pending"
             */
            "paymentStatus" => array(
                "Completed" => "857",
                "Pending" => "859",
            ),

            "happiLIFEScreeningPaymentStatus" => array(
                "Selected" => "1231",
                "Payment Confirmation Pending" => "1233",
                "Cancelled by user" => "1239",
                "Payment Confirmed" => "1235",
                "Added" => "1237",

            ),

            "happiLIFESummaryReadingPaymentStatus" => array(
                "Selected" => "1241",
                "Payment Confirmation Pending" => "1243",
                "Cancelled by user" => "1249",
                "Payment Confirmed" => "1245",
                "Added" => "1247",
            ),

            "happiCHATPaymentStatus" => array(
                "Selected" => "1251",
                "Payment Confirmation Pending" => "1253",
                "Cancelled by user" => "1255",
                "Payment Confirmed" => "1257",
                "Added" => "1259",
            ),

            "happiCHAThappiAPPPaymentStatus" => array(
                "Selected" => "1261",
                "Payment Confirmation Pending" => "1263",
                "Cancelled by user" => "1265",
                "Payment Confirmed" => "1267",
                "Added" => "1269",
            ),

            "happiAPPPaymentStatus" => array(
                "Selected" => "1271",
                "Payment Confirmation Pending" => "1273",
                "Cancelled by user" => "1275",
                "Payment Confirmed" => "1277",
                "Added" => "1279",
            ),

            "happiTALKPaymentStatus" => array(
                "Selected" => "1289",
                "Payment Confirmation Pending" => "1283",
                "Cancelled by user" => "1285",
                "Payment Confirmed" => "1287",
                "Added" => "1281",
            ),

            /**
             * DealType
             * ID: "861", VALUE: "B2B"
             * ID: "863", VALUE: "B2C"
             */
            "dealType" => array(
                "B2B" => "861",
                "B2C" => "863",
            ),
            "contactId" => "CONTACT_ID",


            "dealCategory" => array(
                "B2B_Journey" => "41",
                "B2B" => "3",
                "B2C" => "0",
                "HappiCHAT" => "31",
                "HappiTALK" => "33",
                "ReportReading" => "29",
                "RaisedQuery" => "43",
                "HappiTalkCoordinator" => "58",
            ),

            /** raised query types
             *
             */
            "queryType" => array(
                "screening" => "1372",
                "payment" => "1374",
                "others" => "1376",
                "service" => "1470",
            ),
        );

        /** Lead variables */
        $this->leadVariable = array(
            "gender" => array(
                "Male" => "871",
                "Female" => "873",
                "Others" => "875"
            ),
            "leadType" => array(
                "B2B" => "89",
                "B2C" => "91",
            ),
            "profileType" => array(
                "Salaried"      => "321",
                "Self-Employed" => "323",
                "Home Maker"    => "325",
                "Job Seeker"    => "327",
                "Entrepreneur"  => "329",
                "Frontline Warrior" => "331",
                "Senior Citizen"    => "333",
                "Student (School)"  => "335",
                "Student (college/University)" => "337",
                "Working Women"    => "26776"
            ),
        );

        /** map happimynd packageId to bitrix productId */
        $this->packageIDs = array(
            1 => 43,   // screeening
            2 => 15,   // screening + report
            3 => 5,    // Happichat
            4 => 17,   // Happichat + Happiapp
            5 => 3,    // HappiApp
            6 => 7,    // HappiTalk
            7 => 7,    // HappiTalk
            8 => 7,    // HappiTalk
        );

        /** Default values */
        $this->defaultValues = array(
            "dealCategory" => "0",
        );
    }

    /** Method for PreProcessing the lead data. */
    private function preProcessLeadData($data)
    {
        /** Add account status to active. */
        $data['account_status'] = "1";
        if (isset($data['gender'])) {
            switch ($data['gender']) {
                case "male":
                    $data['gender'] = $this->leadVariable['gender']['Male'];
                    break;
                case "female":
                    $data['gender'] = $this->leadVariable['gender']['Female'];
                    break;
                case "other":
                    $data['gender'] = $this->leadVariable['gender']['Others'];
                    break;
                default:
                    break;
            }
        }
        /** Preprocess profession->user_profile_id */
        if (isset($data['user_profile_id'])) {
            switch ($data['user_profile_id']) {
                case "1":
                    $data['profileType'] = $this->leadVariable['profileType']['Salaried'];
                    break;
                case "2":
                    $data['profileType'] = $this->leadVariable['profileType']['Self-Employed'];
                    break;
                case "3":
                    $data['profileType'] = $this->leadVariable['profileType']['Home Maker'];
                    break;
                case "4":
                    $data['profileType'] = $this->leadVariable['profileType']['Senior Citizen'];
                    break;
                case "5":
                    $data['profileType'] = $this->leadVariable['profileType']['Student (School)'];
                    break;
                case "6":
                    $data['profileType'] = $this->leadVariable['profileType']['Student (college/University)'];
                    break;
                case "7":
                    $data['profileType'] = $this->leadVariable['profileType']['Entrepreneur'];
                    break;
                case "8":
                    $data['profileType'] = $this->leadVariable['profileType']['Job Seeker'];
                    break;
                case "9":
                    $data['profileType'] = $this->leadVariable['profileType']['Frontline Warrior'];
                    break;
                case "10":
                    $data['profileType'] = $this->leadVariable['profileType']['Working Women'];
                    break;  
                default:
                    break;
            }
        }
        return $data;
    }

    /** Method for PreProcessing the deal data. */
    public function preProcessDealData($data, $status)
    {
        if ($status == "create") {
            $data['source'] = "WEB";
            $data['dealType'] = $this->dealVariables['dealType'][$data['dealType'] ?? "B2C"];
            $data['deal'] = "";
        } else if ($status = "update") {
            $data['currency'] = (isset($data['currency'])) ? $data['currency'] : "INR";
        }
        /** Preprocess Deal category */
        if (isset($data['dealCategory'])) {
            if ($data['dealCategory'] == "B2B_Journey")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['B2B_Journey'];
            else if ($data['dealCategory'] == "B2B")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['B2B'];
            else if ($data['dealCategory'] == "HappiCHAT")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['HappiCHAT'];
            else if ($data['dealCategory'] == "HappiTALK")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['HappiTALK'];
            else if ($data['dealCategory'] == "ReportReading")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['ReportReading'];
            else if ($data['dealCategory'] == "RaisedQuery")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['RaisedQuery'];
            else if ($data['dealCategory'] == "HappiTalkCoordinator")
                $data['dealCategory'] = $this->dealVariables['dealCategory']['HappiTalkCoordinator'];
            else
                $data['dealCategory'] = $this->dealVariables['dealCategory']['B2C'];
        }

        /**Prepocess happichat and happitalk data*/

        if (isset($data['HappiCHAT'])) {
            $data['HappiCHAT'] = $data['HappiCHAT'];
        }
        if (isset($data['HappiTALK'])) {
            $data['HappiTALK'] = $data['HappiTALK'];
        }
        if (isset($data['HappiTALKSessions'])) {
            $data['HappiTALKSessions'] = $data['HappiTALKSessions'];
        }

        /** Preprocess payment data */
        if (isset($data['makePayment']))
            $data['makePayment'] = $data['makePayment'];
        if (isset($data['paymentLink']))
            $data['paymentLink'] = $data['paymentLink'];

        /** Preprocess stage  */
        if (isset($data['stage'])) {
            $data['stage'] = $data['stage'];
        }

        /** Preprocess Assessment data */
        if (isset($data['reportLink']))
            $data['reportLink'] = $data['reportLink'];
        if (isset($data['calltime']))
            $data['calltime'] = $data['calltime'];
        if (isset($data['slot']))
            $data['slot'] = $data['slot'];
        if (isset($data['detailLink']))
            $data['detailLink'] = $data['detailLink'];

        /** Preprocess Generate Tokens data */
        if (isset($data['plans']))
            $data['plans'] = $data['plans'];
        if (isset($data['category']))
            $data['category'] = $data['category'];
        if (isset($data['token']))
            $data['token'] = $data['token'];
        if (isset($data['thriveCode']))
            $data['thriveCode'] = $data['thriveCode'];

        /** Preprocess Web notification */
        $data['webNotification'] = (isset($data['webNotification'])) ? $data['webNotification'] : '';

        /** Preprocess Query */
        if (isset($data['queryDescription']))
            $data['queryDescription'] = $data['queryDescription'];
        if (isset($data['queryType']))
            $data['queryType'] = $this->dealVariables['queryType'][$data['queryType']];

        /** Preprocess paymentStatus */
        if (isset($data['paymentStatus']) && $data['paymentStatus']) {
            $data['paymentStatus'] = $this->dealVariables['paymentStatus']['Completed'];
        }

        if (isset($data['paymentDetails'])) {
            if (isset($data['paymentDetails']['happiLIFEScreening'])) {
                if (isset($data['paymentDetails']['happiLIFEScreening']['makePaymentLink'])) {
                    $data['happiLIFEScreeningMakePaymentLink'] = $data['paymentDetails']['happiLIFEScreening']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiLIFEScreening']['paymentStatus'])) {
                    $data['happiLIFEScreeningPaymentStatus'] = $this->dealVariables['happiLIFEScreeningPaymentStatus'][$data['paymentDetails']['happiLIFEScreening']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiLIFEScreening']['paymentOrderLink'])) {
                    $data['happiLIFEScreeningPaymentOrderLink'] = $data['paymentDetails']['happiLIFEScreening']['paymentOrderLink'];
                }
            }

            if (isset($data['paymentDetails']['happiLIFESummaryReading'])) {
                if (isset($data['paymentDetails']['happiLIFESummaryReading']['makePaymentLink'])) {
                    $data['happiLIFESummaryReadingMakePaymentLink'] = $data['paymentDetails']['happiLIFESummaryReading']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiLIFESummaryReading']['paymentStatus'])) {
                    $data['happiLIFESummaryReadingPaymentStatus'] = $this->dealVariables['happiLIFESummaryReadingPaymentStatus'][$data['paymentDetails']['happiLIFESummaryReading']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiLIFESummaryReading']['paymentOrderLink'])) {
                    $data['happiLIFESummaryReadingPaymentOrderLink'] = $data['paymentDetails']['happiLIFESummaryReading']['paymentOrderLink'];
                }
            }

            if (isset($data['paymentDetails']['happiAPP'])) {
                if (isset($data['paymentDetails']['happiAPP']['makePaymentLink'])) {
                    $data['happiAPPMakePaymentLink'] = $data['paymentDetails']['happiAPP']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiAPP']['paymentStatus'])) {
                    $data['happiAPPPaymentStatus'] = $this->dealVariables['happiAPPPaymentStatus'][$data['paymentDetails']['happiAPP']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiAPP']['paymentOrderLink'])) {
                    $data['happiAPPPaymentOrderLink'] = $data['paymentDetails']['happiAPP']['paymentOrderLink'];
                }
            }

            if (isset($data['paymentDetails']['happiTALK'])) {
                if (isset($data['paymentDetails']['happiTALK']['makePaymentLink'])) {
                    $data['happiTALKMakePaymentLink'] = $data['paymentDetails']['happiTALK']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiTALK']['paymentStatus'])) {
                    $data['happiTALKPaymentStatus'] = $this->dealVariables['happiTALKPaymentStatus'][$data['paymentDetails']['happiTALK']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiTALK']['paymentOrderLink'])) {
                    $data['happiTALKPaymentOrderLink'] = $data['paymentDetails']['happiTALK']['paymentOrderLink'];
                }
            }

            if (isset($data['paymentDetails']['happiCHAT'])) {
                if (isset($data['paymentDetails']['happiCHAT']['makePaymentLink'])) {
                    $data['happiCHATMakePaymentLink'] = $data['paymentDetails']['happiCHAT']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiCHAT']['paymentStatus'])) {
                    $data['happiCHATPaymentStatus'] = $this->dealVariables['happiCHATPaymentStatus'][$data['paymentDetails']['happiCHAT']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiCHAT']['paymentOrderLink'])) {
                    $data['happiCHATPaymentOrderLink'] = $data['paymentDetails']['happiCHAT']['paymentOrderLink'];
                }
            }

            if (isset($data['paymentDetails']['happiCHAThappiAPP'])) {
                if (isset($data['paymentDetails']['happiCHAThappiAPP']['makePaymentLink'])) {
                    $data['happiCHAThappiAPPMakePaymentLink'] = $data['paymentDetails']['happiCHAThappiAPP']['makePaymentLink'];
                }
                if (isset($data['paymentDetails']['happiCHAThappiAPP']['paymentStatus'])) {
                    $data['happiCHAThappiAPPPaymentStatus'] = $this->dealVariables['happiCHAThappiAPPPaymentStatus'][$data['paymentDetails']['happiCHAThappiAPP']['paymentStatus']];
                }
                if (isset($data['paymentDetails']['happiCHAThappiAPP']['paymentOrderLink'])) {
                    $data['happiCHAThappiAPPPaymentOrderLink'] = $data['paymentDetails']['happiCHAThappiAPP']['paymentOrderLink'];
                }
            }
        }
        /** Preprocess gender */
        if (isset($data['gender'])) {
            switch ($data['gender']) {
                case "male":
                    $data['gender'] = $this->dealVariables['gender']['Male'];
                    break;
                case "female":
                    $data['gender'] = $this->dealVariables['gender']['Female'];
                    break;
                case "other":
                    $data['gender'] = $this->dealVariables['gender']['Others'];
                    break;
                default:
                    break;
            }
        }
        /** Preprocess profession->user_profile_id */
        if (isset($data['user_profile_id'])) {
            switch ($data['user_profile_id']) {
                case "1":
                    $data['profileType'] = $this->dealVariables['profileType']['Salaried'];
                    break;
                case "2":
                    $data['profileType'] = $this->dealVariables['profileType']['Self-Employed'];
                    break;
                case "3":
                    $data['profileType'] = $this->dealVariables['profileType']['Home Maker'];
                    break;
                case "4":
                    $data['profileType'] = $this->dealVariables['profileType']['Senior Citizen'];
                    break;
                case "5":
                    $data['profileType'] = $this->dealVariables['profileType']['Student (School)'];
                    break;
                case "6":
                    $data['profileType'] = $this->dealVariables['profileType']['Student (college/University)'];
                    break;
                case "7":
                    $data['profileType'] = $this->dealVariables['profileType']['Entrepreneur'];
                    break;
                case "8":
                    $data['profileType'] = $this->dealVariables['profileType']['Job Seeker'];
                    break;
                case "9":
                    $data['profileType'] = $this->dealVariables['profileType']['Frontline Warrior'];
                    break;
                case "10":
                    $data['profileType'] = $this->dealVariables['profileType']['Working Women'];
                    break;  
                default:
                    break;
            }
        }
        /** Return the data. */
        return $data;
    }

    /** Method for PreProcessing the product deal data. */
    public function preProcessProductDeal($datas)
    {
        $returnData = array();
        foreach ($datas as $data) {
            // array_push($returnData, array(
            //     /** update & add the data according to bitrix data */
            //     $this->dealProductAttributes['packageId'] => $this->packageIDs[$data['package_id']],
            //     $this->dealProductAttributes['price'] => $data['price'],
            //     $this->dealProductAttributes['quantity'] => $data['quantity'],
            // ));

            array_push($returnData, array(
                /** update & add the data according to bitrix data */
                $this->dealProductAttributes['packageId'] => $data['package_id'],
                $this->dealProductAttributes['price'] => $data['price'],
                $this->dealProductAttributes['quantity'] => $data['quantity'],
            ));

        }

        /** Return updated data */
        return $returnData;
    }

    /** method for add the lead */
    public function addLead($data, $individual = false, $organization = "")
    {
        /** Defining method to add lead */
        $method = 'crm.lead.add.json';

        /** Preprocess the lead data */
        $data = $this->preProcessLeadData($data);

        $leadType = ($organization != "") ? "B2B" : "B2C";

        /** Create a lead. */
        $response = Http::get($this->webhook . $method, [
            $this->leadAttributes["username"]       => isset($data['username']) ? $data['username'] : "",
            $this->leadAttributes["nickname"]       => isset($data['nickname']) ? $data['nickname'] : "",
            $this->leadAttributes["email"]          => isset($data['email']) ? $data['email'] : "",
            $this->leadAttributes["profileType"]    => isset($data['profileType']) ? $data['profileType'] : "",
            $this->leadAttributes["age"]            => isset($data['age']) ? $data['age'] : "",
            $this->leadAttributes["gender"]         => isset($data['gender']) ? $data['gender'] : "",
            $this->leadAttributes["mobile"]         => isset($data['mobile']) ? $data['mobile'] : "",
            $this->leadAttributes["account_status"] => isset($data['account_status']) ? $data['account_status'] : "",
            $this->leadAttributes["lead_type"]      => $this->leadVariable['leadType'][$leadType],
            $this->leadAttributes["individual"]     => $individual,
            $this->leadAttributes["organization"]   => $organization,
        ]);

        /** Return the Bitrix response. */
        return json_decode($response->body());
    }

    /** Method for fetch the lead */
    public function getLead($id)
    {
        $method = 'crm.lead.get.json';
        $response = Http::get($this->webhook . $method, [
            "id" => $id
        ]);
        return json_decode($response->body());
    }

    /** Method for update the lead */
    public function updateLead($id, $user)
    {
        /** Defining method to add lead */
        $method = 'crm.lead.update.json';

        /** Preprocess the lead data */
        $user = $this->preProcessLeadData($user);

        /** Update the lead. */
        $response = Http::get($this->webhook . $method, [
            "id" => $id,
            $this->leadAttributes["username"]       => $user['username'],
            $this->leadAttributes["nickname"]       => $user['nickname'],
            $this->leadAttributes["email"]          => $user['email'],
            $this->leadAttributes["profileType"]     => $user['profileType'],
            $this->leadAttributes["age"]            => $user['age'],
            $this->leadAttributes["gender"]         => $user['gender'],
            $this->leadAttributes["mobile"]         => $user['mobile'],
            $this->leadAttributes["account_status"] => $user['account_status'],
        ]);
        return json_decode($response->body());
    }

    /** method for add the lead */
    public function addDeal($data, $leadId = "", $organizationName = "")
    {
        /** Defining method to add deal */
        $method = 'crm.deal.add.json';

        $data = $this->preProcessDealData($data, "create");

        /** Create a deal. */
        $response = Http::get($this->webhook . $method, [
            $this->dealAttributes['leadId']         => $leadId,
            $this->dealAttributes['username']       => isset($data['username']) ? $data['username'] : "",
            $this->dealAttributes['nickname']       => isset($data['nickname']) ? $data['nickname'] : "",
            $this->dealAttributes['age']            => isset($data['age']) ? $data['age'] : "",
            $this->dealAttributes['gender']         => isset($data['gender']) ? $data['gender'] : "",
            $this->dealAttributes['profileType']    => isset($data['profileType']) ? $data['profileType'] : "",
            $this->dealAttributes['dealCategory']   => isset($data['dealCategory']) ? $data['dealCategory'] : $this->defaultValues['dealCategory'],
            $this->dealAttributes['organization']   => $organizationName,

            $this->dealAttributes['deal']           => isset($data['deal']) ? $data['deal'] : "",
            $this->dealAttributes['dealType']       => isset($data['dealType']) ? $data['dealType'] : "",
            $this->dealAttributes['stage']          => isset($data['stage']) ? $data['stage'] : "",
            $this->dealAttributes['source']         => isset($data['source']) ? $data['source'] : "",
            $this->dealAttributes['paymentStatus']  => isset($data['paymentStatus']) ? $data['paymentStatus'] : "",

            $this->dealAttributes['reportReadingSlotDate']  => isset($data['reportReadingSlotDate']) ? $data['reportReadingSlotDate'] : "",
            $this->dealAttributes['reportReadingTimeSlot']  => isset($data['reportReadingTimeSlot']) ? $data['reportReadingTimeSlot'] : "",
            $this->dealAttributes['reportReadingCommunicationMode']  => isset($data['reportReadingCommunicationMode']) ? $data['reportReadingCommunicationMode'] : "",
            $this->dealAttributes['detailLink']     => isset($data['detailLink']) ? $data['detailLink'] : "",

            $this->dealAttributes['queryDescription'] => isset($data['queryDescription']) ? $data['queryDescription'] : "",
            $this->dealAttributes['queryType'] => isset($data['queryType']) ? $data['queryType'] : "",

            $this->dealAttributes['TotalHappiTalkSessionAppointed']  => isset($data['session_appointed']) ? $data['session_appointed'] : "",
            $this->dealAttributes['HappiTalkSessionAvailed']  => isset($data['session_availed']) ? $data['session_availed'] : 0,
            $this->dealAttributes['HappiTalkSessionRemaining']  => isset($data['session_remaining']) ? $data['session_remaining'] : "",
            $this->dealAttributes['Psychologist1BasePrice']  => isset($data['base_price']) ? $data['base_price'] : "",
            $this->dealAttributes['BookingTime']  => isset($data['booking_date']) ? $data['booking_date'] : "",
            $this->dealAttributes['BookedPsychologistName']  => isset($data['booked_psychologist_name']) ? $data['booked_psychologist_name'] : "",
            $this->dealAttributes['PsychologistSlot']  => isset($data['slot']) ? $data['slot'] : ""
        ]);

        /** Return the Bitrix response. */
        return json_decode($response->body());
    }

    public function updateDeal($id, $user = '', $data)
    {
        /** Defining method to update deal */
        $method = 'crm.deal.update.json';
        \Log::info('before preprocess' . json_encode($data));
        if ($user != '')
            $user = $this->preProcessDealData($user, "update");
        $data = $this->preProcessDealData($data, "update");
        \Log::info('after preprocess' . json_encode($data));
        /** Get the previous deal */
        $preDealData = $this->getPreviousDeal($id);
        \Log::info('predeal data' . json_encode($preDealData));
        /** update the raise query as an array parameter*/
        if (isset($data['query'])) {
            /** Fetch if existing Support Customer Ticket is there or not.! */
            $existingSuppCusId = $this->getExistingCustomerSupportID($id);
            $existingSuppCusId = ($existingSuppCusId != 0) ? $existingSuppCusId : $id;

            $parameter = [];
            $parameter[] = "id=" . $existingSuppCusId;
            foreach ($data['query'] as $q) {
                $parameter[] = $this->dealAttributes['query'] . '=' . $q;
            }
            $parameter = implode('&', $parameter);
            $url = $this->webhook . $method . '?' . $parameter;
            Http::get($url);
        }
        // dd($preDealData);

        /** update the with additional deal data. */
        $response = Http::get($this->webhook . $method, [
            "id" => $id,
            $this->dealAttributes['username']       => ($user != '' && isset($user['username'])) ? $user['username'] : $preDealData[$this->dealInverseAttributes['username']],
            $this->dealAttributes['nickname']       => ($user != '' && isset($user['nickname'])) ? $user['nickname'] : $preDealData[$this->dealInverseAttributes['nickname']],
            $this->dealAttributes['age']            => ($user != '' && isset($user['age'])) ? $user['age'] : $preDealData[$this->dealInverseAttributes['age']],
            $this->dealAttributes['gender']         => ($user != '' && isset($user['gender'])) ? $user['gender'] : $preDealData[$this->dealInverseAttributes['gender']],
            $this->dealAttributes['dealCategory']   => isset($data['dealCategory']) ? $data['dealCategory'] : $preDealData[$this->dealInverseAttributes['dealCategory']],

            $this->dealAttributes['stage']          => isset($data['stage']) ? $data['stage'] : $preDealData[$this->dealInverseAttributes['stage']],
            $this->dealAttributes['contactId']      => isset($data['contactId']) ? $data['contactId'] : $preDealData[$this->dealInverseAttributes['contactId']],
            $this->dealAttributes['paymentStatus']  => isset($data['paymentStatus']) ? $data['paymentStatus'] : $preDealData[$this->dealInverseAttributes['paymentStatus']],
            $this->dealAttributes['paymentLink']    => isset($data['paymentLink']) ? $data['paymentLink'] : $preDealData[$this->dealInverseAttributes['paymentLink']],


            $this->dealAttributes['happiLIFEScreeningMakePaymentLink']    => isset($data['happiLIFEScreeningMakePaymentLink']) ? $data['happiLIFEScreeningMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningMakePaymentLink']],
            $this->dealAttributes['happiLIFEScreeningPaymentOrderLink']    => isset($data['happiLIFEScreeningPaymentOrderLink']) ? $data['happiLIFEScreeningPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningPaymentOrderLink']],
            $this->dealAttributes['happiLIFEScreeningPaymentStatus']    => isset($data['happiLIFEScreeningPaymentStatus']) ? $data['happiLIFEScreeningPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningPaymentStatus']],

            $this->dealAttributes['happiLIFESummaryReadingMakePaymentLink']    => isset($data['happiLIFESummaryReadingMakePaymentLink']) ? $data['happiLIFESummaryReadingMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingMakePaymentLink']],
            $this->dealAttributes['happiLIFESummaryReadingPaymentOrderLink']    => isset($data['happiLIFESummaryReadingPaymentOrderLink']) ? $data['happiLIFESummaryReadingPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingPaymentOrderLink']],
            $this->dealAttributes['happiLIFESummaryReadingPaymentStatus']    => isset($data['happiLIFESummaryReadingPaymentStatus']) ? $data['happiLIFESummaryReadingPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingPaymentStatus']],

            $this->dealAttributes['happiCHATMakePaymentLink']    => isset($data['happiCHATMakePaymentLink']) ? $data['happiCHATMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiCHATMakePaymentLink']],
            $this->dealAttributes['happiCHATPaymentOrderLink']    => isset($data['happiCHATPaymentOrderLink']) ? $data['happiCHATPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiCHATPaymentOrderLink']],
            $this->dealAttributes['happiCHATPaymentStatus']    => isset($data['happiCHATPaymentStatus']) ? $data['happiCHATPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiCHATPaymentStatus']],

            $this->dealAttributes['happiCHAThappiAPPMakePaymentLink']    => isset($data['happiCHAThappiAPPMakePaymentLink']) ? $data['happiCHAThappiAPPMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPMakePaymentLink']],
            $this->dealAttributes['happiCHAThappiAPPPaymentOrderLink']    => isset($data['happiCHAThappiAPPPaymentOrderLink']) ? $data['happiCHAThappiAPPPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPPaymentOrderLink']],
            $this->dealAttributes['happiCHAThappiAPPPaymentStatus']    => isset($data['happiCHAThappiAPPPaymentStatus']) ? $data['happiCHAThappiAPPPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPPaymentStatus']],

            $this->dealAttributes['happiAPPMakePaymentLink']    => isset($data['happiAPPMakePaymentLink']) ? $data['happiAPPMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiAPPMakePaymentLink']],
            $this->dealAttributes['happiAPPPaymentOrderLink']    => isset($data['happiAPPPaymentOrderLink']) ? $data['happiAPPPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiAPPPaymentOrderLink']],
            $this->dealAttributes['happiAPPPaymentStatus']    => isset($data['happiAPPPaymentStatus']) ? $data['happiAPPPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiAPPPaymentStatus']],

            $this->dealAttributes['happiTALKMakePaymentLink']    => isset($data['happiTALKMakePaymentLink']) ? $data['happiTALKMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiTALKMakePaymentLink']],
            $this->dealAttributes['happiTALKPaymentOrderLink']    => isset($data['happiTALKPaymentOrderLink']) ? $data['happiTALKPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiTALKPaymentOrderLink']],
            $this->dealAttributes['happiTALKPaymentStatus']    => isset($data['happiTALKPaymentStatus']) ? $data['happiTALKPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiTALKPaymentStatus']],

            $this->dealAttributes['makePayment']    => isset($data['makePayment']) ? $data['makePayment'] : $preDealData[$this->dealInverseAttributes['makePayment']],
            $this->dealAttributes['currency']       => isset($data['currency']) ? $data['currency'] : $preDealData[$this->dealInverseAttributes['currency']],

            $this->dealAttributes['reportLink']     => isset($data['reportLink']) ? $data['reportLink'] : $preDealData[$this->dealInverseAttributes['reportLink']],
            $this->dealAttributes['calltime']       => isset($data['calltime']) ? $data['calltime'] : $preDealData[$this->dealInverseAttributes['calltime']],
            $this->dealAttributes['slot']           => isset($data['slot']) ? $data['slot'] : $preDealData[$this->dealInverseAttributes['slot']],
            $this->dealAttributes['detailLink']     => isset($data['detailLink']) ? $data['detailLink'] : $preDealData[$this->dealInverseAttributes['detailLink']],

            $this->dealAttributes['webNotification'] => isset($data['webNotification']) ? $data['webNotification'] : $preDealData[$this->dealInverseAttributes['webNotification']],

            $this->dealAttributes['plans']        => isset($data['plans']) ? $data['plans'] : $preDealData[$this->dealInverseAttributes['plans']],
            $this->dealAttributes['category']     => isset($data['category']) ? $data['category'] : $preDealData[$this->dealInverseAttributes['category']],
            $this->dealAttributes['token']        => isset($data['token']) ? $data['token'] : $preDealData[$this->dealInverseAttributes['token']],
            $this->dealAttributes['thriveCode']   => isset($data['thriveCode']) ? $data['thriveCode'] : $preDealData[$this->dealInverseAttributes['thriveCode']],
            $this->dealAttributes['amount']       => isset($data['amount']) ? $data['amount'] + intval($preDealData[$this->dealInverseAttributes['amount']]) : $preDealData[$this->dealInverseAttributes['amount']],

            $this->dealAttributes['HappiCHAT'] => (isset($data['HappiCHAT']) && $data['HappiCHAT'] != '') ? $data['HappiCHAT'] : $preDealData[$this->dealInverseAttributes['HappiCHAT']],
            $this->dealAttributes['HappiTALK'] => (isset($data['HappiTALK']) && $data['HappiTALK'] != '') ? $data['HappiTALK'] : $preDealData[$this->dealInverseAttributes['HappiTALK']],
            $this->dealAttributes['HappiTALKSessions'] => (isset($data['HappiTALKSessions']) && $data['HappiTALKSessions'] != '') ? $data['HappiTALKSessions'] : $preDealData[$this->dealInverseAttributes['HappiTALKSessions']],
        ]);
        $t = [
            "id" => $id,
            $this->dealAttributes['username']       => ($user != '' && isset($user['username'])) ? $user['username'] : $preDealData[$this->dealInverseAttributes['username']],
            $this->dealAttributes['nickname']       => ($user != '' && isset($user['nickname'])) ? $user['nickname'] : $preDealData[$this->dealInverseAttributes['nickname']],
            $this->dealAttributes['age']            => ($user != '' && isset($user['age'])) ? $user['age'] : $preDealData[$this->dealInverseAttributes['age']],
            $this->dealAttributes['gender']         => ($user != '' && isset($user['gender'])) ? $user['gender'] : $preDealData[$this->dealInverseAttributes['gender']],
            $this->dealAttributes['dealCategory']   => isset($data['dealCategory']) ? $data['dealCategory'] : $preDealData[$this->dealInverseAttributes['dealCategory']],

            $this->dealAttributes['stage']          => isset($data['stage']) ? $data['stage'] : $preDealData[$this->dealInverseAttributes['stage']],
            $this->dealAttributes['contactId']      => isset($data['contactId']) ? $data['contactId'] : $preDealData[$this->dealInverseAttributes['contactId']],
            $this->dealAttributes['paymentStatus']  => isset($data['paymentStatus']) ? $data['paymentStatus'] : $preDealData[$this->dealInverseAttributes['paymentStatus']],
            $this->dealAttributes['paymentLink']    => isset($data['paymentLink']) ? $data['paymentLink'] : $preDealData[$this->dealInverseAttributes['paymentLink']],


            $this->dealAttributes['happiLIFEScreeningMakePaymentLink']    => isset($data['happiLIFEScreeningMakePaymentLink']) ? $data['happiLIFEScreeningMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningMakePaymentLink']],
            $this->dealAttributes['happiLIFEScreeningPaymentOrderLink']    => isset($data['happiLIFEScreeningPaymentOrderLink']) ? $data['happiLIFEScreeningPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningMakePaymentLink']],
            $this->dealAttributes['happiLIFEScreeningPaymentStatus']    => isset($data['happiLIFEScreeningPaymentStatus']) ? $data['happiLIFEScreeningPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiLIFEScreeningPaymentStatus']],

            $this->dealAttributes['happiLIFESummaryReadingMakePaymentLink']    => isset($data['happiLIFESummaryReadingMakePaymentLink']) ? $data['happiLIFESummaryReadingMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingMakePaymentLink']],
            $this->dealAttributes['happiLIFESummaryReadingPaymentOrderLink']    => isset($data['happiLIFESummaryReadingPaymentOrderLink']) ? $data['happiLIFESummaryReadingPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingPaymentOrderLink']],
            $this->dealAttributes['happiLIFESummaryReadingPaymentStatus']    => isset($data['happiLIFESummaryReadingPaymentStatus']) ? $data['happiLIFESummaryReadingPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiLIFESummaryReadingPaymentStatus']],

            $this->dealAttributes['happiCHATMakePaymentLink']    => isset($data['happiCHATMakePaymentLink']) ? $data['happiCHATMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiCHATMakePaymentLink']],
            $this->dealAttributes['happiCHATPaymentOrderLink']    => isset($data['happiCHATPaymentOrderLink']) ? $data['happiCHATPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiCHATPaymentOrderLink']],
            $this->dealAttributes['happiCHATPaymentStatus']    => isset($data['happiCHATPaymentStatus']) ? $data['happiCHATPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiCHATPaymentStatus']],

            $this->dealAttributes['happiCHAThappiAPPMakePaymentLink']    => isset($data['happiCHAThappiAPPMakePaymentLink']) ? $data['happiCHAThappiAPPMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPMakePaymentLink']],
            $this->dealAttributes['happiCHAThappiAPPPaymentOrderLink']    => isset($data['happiCHAThappiAPPPaymentOrderLink']) ? $data['happiCHAThappiAPPPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPPaymentOrderLink']],
            $this->dealAttributes['happiCHAThappiAPPPaymentStatus']    => isset($data['happiCHAThappiAPPPaymentStatus']) ? $data['happiCHAThappiAPPPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiCHAThappiAPPPaymentStatus']],

            $this->dealAttributes['happiAPPMakePaymentLink']    => isset($data['happiAPPMakePaymentLink']) ? $data['happiAPPMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiAPPMakePaymentLink']],
            $this->dealAttributes['happiAPPPaymentOrderLink']    => isset($data['happiAPPPaymentOrderLink']) ? $data['happiAPPPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiAPPPaymentOrderLink']],
            $this->dealAttributes['happiAPPPaymentStatus']    => isset($data['happiAPPPaymentStatus']) ? $data['happiAPPPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiAPPPaymentStatus']],

            $this->dealAttributes['happiTALKMakePaymentLink']    => isset($data['happiTALKMakePaymentLink']) ? $data['happiTALKMakePaymentLink'] : $preDealData[$this->dealInverseAttributes['happiTALKMakePaymentLink']],
            $this->dealAttributes['happiTALKPaymentOrderLink']    => isset($data['happiTALKPaymentOrderLink']) ? $data['happiTALKPaymentOrderLink'] : $preDealData[$this->dealInverseAttributes['happiTALKPaymentOrderLink']],
            $this->dealAttributes['happiTALKPaymentStatus']    => isset($data['happiTALKPaymentStatus']) ? $data['happiTALKPaymentStatus'] : $preDealData[$this->dealInverseAttributes['happiTALKPaymentStatus']],

            $this->dealAttributes['makePayment']    => isset($data['makePayment']) ? $data['makePayment'] : $preDealData[$this->dealInverseAttributes['makePayment']],
            $this->dealAttributes['currency']       => isset($data['currency']) ? $data['currency'] : $preDealData[$this->dealInverseAttributes['currency']],

            $this->dealAttributes['reportLink']     => isset($data['reportLink']) ? $data['reportLink'] : $preDealData[$this->dealInverseAttributes['reportLink']],
            $this->dealAttributes['calltime']       => isset($data['calltime']) ? $data['calltime'] : $preDealData[$this->dealInverseAttributes['calltime']],
            $this->dealAttributes['slot']           => isset($data['slot']) ? $data['slot'] : $preDealData[$this->dealInverseAttributes['slot']],
            $this->dealAttributes['detailLink']     => isset($data['detailLink']) ? $data['detailLink'] : $preDealData[$this->dealInverseAttributes['detailLink']],

            $this->dealAttributes['webNotification'] => isset($data['webNotification']) ? $data['webNotification'] : $preDealData[$this->dealInverseAttributes['webNotification']],

            $this->dealAttributes['plans']        => isset($data['plans']) ? $data['plans'] : $preDealData[$this->dealInverseAttributes['plans']],
            $this->dealAttributes['category']     => isset($data['category']) ? $data['category'] : $preDealData[$this->dealInverseAttributes['category']],
            $this->dealAttributes['token']        => isset($data['token']) ? $data['token'] : $preDealData[$this->dealInverseAttributes['token']],
            $this->dealAttributes['thriveCode']   => isset($data['thriveCode']) ? $data['thriveCode'] : $preDealData[$this->dealInverseAttributes['thriveCode']],
            $this->dealAttributes['amount']       => isset($data['amount']) ? $data['amount'] + intval($preDealData[$this->dealInverseAttributes['amount']]) : $preDealData[$this->dealInverseAttributes['amount']],

            $this->dealAttributes['HappiCHAT'] => isset($data['HappiCHAT']) ? $data['HappiCHAT'] : $preDealData[$this->dealInverseAttributes['HappiCHAT']],
            $this->dealAttributes['HappiTALK'] => isset($data['HappiTALK']) ? $data['HappiTALK'] : $preDealData[$this->dealInverseAttributes['HappiTALK']],
            $this->dealAttributes['HappiTALKSessions'] => isset($data['HappiTALKSessions']) ? $data['HappiTALKSessions'] : $preDealData[$this->dealInverseAttributes['HappiTALKSessions']],
        ];
        \Log::info('data sent is ' . json_encode($t));
        /** Return the Bitrix response. */
        return json_decode($response->body());
    }

    public function getDeal($id)
    {
        $method = 'crm.deal.get.json';
        $response = Http::get($this->webhook . $method, [
            "id" => $id
        ]);
        return json_decode($response->body());
    }

    public function getPreviousDeal($id)
    {
        $deal = $this->getDeal($id);
        $preDealData = array();
        if (!is_null($deal->result)) {
            $username = $this->dealInverseAttributes['username'];
            $nickname = $this->dealInverseAttributes['nickname'];
            $age = $this->dealInverseAttributes['age'];
            $gender = $this->dealInverseAttributes['gender'];
            $stage = $this->dealInverseAttributes['stage'];
            $contactId = $this->dealInverseAttributes['contactId'];
            $paymentStatus = $this->dealInverseAttributes['paymentStatus'];
            $paymentLink = $this->dealInverseAttributes['paymentLink'];
            $makePayment = $this->dealInverseAttributes['makePayment'];
            $reportLink = $this->dealInverseAttributes['reportLink'];
            $calltime = $this->dealInverseAttributes['calltime'];
            $slot = $this->dealInverseAttributes['slot'];
            $detailLink = $this->dealInverseAttributes['detailLink'];
            $webNotification = $this->dealInverseAttributes['webNotification'];
            $plans = $this->dealInverseAttributes['plans'];
            $category = $this->dealInverseAttributes['category'];
            $token = $this->dealInverseAttributes['token'];
            $thriveCode = $this->dealInverseAttributes['thriveCode'];
            $queryType = $this->dealInverseAttributes['queryType'];
            $queryDescription = $this->dealInverseAttributes['queryDescription'];
            $dealCategory = $this->dealInverseAttributes['dealCategory'];
            $happiTalk = $this->dealInverseAttributes['HappiTALK'];
            $happiTalkSessions = $this->dealInverseAttributes['HappiTALKSessions'];
            $happiChat = $this->dealInverseAttributes['HappiCHAT'];
            $amount = $this->dealInverseAttributes['amount'];

            $happiLIFEScreeningMakePaymentLink = $this->dealInverseAttributes['happiLIFEScreeningMakePaymentLink'];
            $happiLIFEScreeningPaymentOrderLink = $this->dealInverseAttributes['happiLIFEScreeningPaymentOrderLink'];
            $happiLIFEScreeningPaymentStatus = $this->dealInverseAttributes['happiLIFEScreeningPaymentStatus'];

            $happiLIFESummaryReadingMakePaymentLink = $this->dealInverseAttributes['happiLIFESummaryReadingMakePaymentLink'];
            $happiLIFESummaryReadingPaymentOrderLink = $this->dealInverseAttributes['happiLIFESummaryReadingPaymentOrderLink'];
            $happiLIFESummaryReadingPaymentStatus = $this->dealInverseAttributes['happiLIFESummaryReadingPaymentStatus'];

            $happiCHATMakePaymentLink = $this->dealInverseAttributes['happiCHATMakePaymentLink'];
            $happiCHATPaymentOrderLink = $this->dealInverseAttributes['happiCHATPaymentOrderLink'];
            $happiCHATPaymentStatus = $this->dealInverseAttributes['happiCHATPaymentStatus'];

            $happiCHAThappiAPPMakePaymentLink = $this->dealInverseAttributes['happiCHAThappiAPPMakePaymentLink'];
            $happiCHAThappiAPPPaymentOrderLink = $this->dealInverseAttributes['happiCHAThappiAPPPaymentOrderLink'];
            $happiCHAThappiAPPPaymentStatus = $this->dealInverseAttributes['happiCHAThappiAPPPaymentStatus'];

            $happiAPPMakePaymentLink = $this->dealInverseAttributes['happiAPPMakePaymentLink'];
            $happiAPPPaymentOrderLink = $this->dealInverseAttributes['happiAPPPaymentOrderLink'];
            $happiAPPPaymentStatus = $this->dealInverseAttributes['happiAPPPaymentStatus'];

            $happiTALKMakePaymentLink = $this->dealInverseAttributes['happiTALKMakePaymentLink'];
            $happiTALKPaymentOrderLink = $this->dealInverseAttributes['happiTALKPaymentOrderLink'];
            $happiTALKPaymentStatus = $this->dealInverseAttributes['happiTALKPaymentStatus'];

            $preDealData[$username]         = isset($deal->result->$username) ? $deal->result->$username : "";
            $preDealData[$nickname]         = isset($deal->result->$nickname) ? $deal->result->$nickname : "";
            $preDealData[$age]              = isset($deal->result->$age) ? $deal->result->$age : "";
            $preDealData[$gender]           = isset($deal->result->$gender) ? $deal->result->$gender : "";
            $preDealData[$stage]            = isset($deal->result->$stage) ? $deal->result->$stage : "";
            $preDealData[$contactId]        = isset($deal->result->$contactId) ? $deal->result->$contactId : "";
            $preDealData[$paymentStatus]    = isset($deal->result->$paymentStatus) ? $deal->result->$paymentStatus : "";
            $preDealData[$paymentLink]      = isset($deal->result->$paymentLink) ? $deal->result->$paymentLink : "";
            $preDealData[$makePayment]      = isset($deal->result->$makePayment) ? $deal->result->$makePayment : "";
            $preDealData[$reportLink]       = isset($deal->result->$reportLink) ? $deal->result->$reportLink : "";
            $preDealData[$slot]             = isset($deal->result->$slot) ? $deal->result->$slot : "";
            $preDealData[$calltime]         = isset($deal->result->$slot) ? $deal->result->$calltime : "";
            $preDealData[$detailLink]       = isset($deal->result->$detailLink) ? $deal->result->$detailLink : "";
            $preDealData[$webNotification]  = isset($deal->result->$webNotification) ? $deal->result->$webNotification : "";
            $preDealData[$plans]            = isset($deal->result->$plans) ? $deal->result->$plans : "";
            $preDealData[$category]         = isset($deal->result->$category) ? $deal->result->$category : "";
            $preDealData[$token]            = isset($deal->result->$token) ? $deal->result->$token : "";
            $preDealData[$thriveCode]       = isset($deal->result->$thriveCode) ? $deal->result->$thriveCode : "";
            $preDealData[$queryType]            = isset($deal->result->$queryType) ? $deal->result->$queryType : "";
            $preDealData[$queryDescription]            = isset($deal->result->$queryDescription) ? $deal->result->$queryDescription : "";
            $preDealData[$dealCategory]     = isset($deal->result->$dealCategory) ? $deal->result->$dealCategory : $this->defaultValues['dealCategory'];
            $preDealData[$happiChat]        = isset($deal->result->$happiChat) ? $deal->result->$happiChat : "";
            $preDealData[$happiTalk]        = isset($deal->result->$happiTalk) ? $deal->result->$happiTalk : "";
            $preDealData[$happiTalkSessions]        = isset($deal->result->$happiTalkSessions) ? $deal->result->$happiTalkSessions : "";
            $preDealData[$amount]           = isset($deal->result->$amount) ? $deal->result->$amount : "";

            $preDealData[$happiLIFEScreeningMakePaymentLink]           = isset($deal->result->$happiLIFEScreeningMakePaymentLink) ? $deal->result->$happiLIFEScreeningMakePaymentLink : "";
            $preDealData[$happiLIFEScreeningPaymentOrderLink]           = isset($deal->result->$happiLIFEScreeningPaymentOrderLink) ? $deal->result->$happiLIFEScreeningPaymentOrderLink : "";
            $preDealData[$happiLIFEScreeningPaymentStatus]           = isset($deal->result->$happiLIFEScreeningPaymentStatus) ? $deal->result->$happiLIFEScreeningPaymentStatus : "";

            $preDealData[$happiLIFESummaryReadingMakePaymentLink]           = isset($deal->result->$happiLIFESummaryReadingMakePaymentLink) ? $deal->result->$happiLIFESummaryReadingMakePaymentLink : "";
            $preDealData[$happiLIFESummaryReadingPaymentOrderLink]           = isset($deal->result->$happiLIFESummaryReadingPaymentOrderLink) ? $deal->result->$happiLIFESummaryReadingPaymentOrderLink : "";
            $preDealData[$happiLIFESummaryReadingPaymentStatus]           = isset($deal->result->$happiLIFESummaryReadingPaymentStatus) ? $deal->result->$happiLIFESummaryReadingPaymentStatus : "";

            $preDealData[$happiCHATMakePaymentLink]           = isset($deal->result->$happiCHATMakePaymentLink) ? $deal->result->$happiCHATMakePaymentLink : "";
            $preDealData[$happiCHATPaymentOrderLink]           = isset($deal->result->$happiCHATPaymentOrderLink) ? $deal->result->$happiCHATPaymentOrderLink : "";
            $preDealData[$happiCHATPaymentStatus]           = isset($deal->result->$happiCHATPaymentStatus) ? $deal->result->$happiCHATPaymentStatus : "";

            $preDealData[$happiCHAThappiAPPMakePaymentLink]           = isset($deal->result->$happiCHAThappiAPPMakePaymentLink) ? $deal->result->$happiCHAThappiAPPMakePaymentLink : "";
            $preDealData[$happiCHAThappiAPPPaymentOrderLink]           = isset($deal->result->$happiCHAThappiAPPPaymentOrderLink) ? $deal->result->$happiCHAThappiAPPPaymentOrderLink : "";
            $preDealData[$happiCHAThappiAPPPaymentStatus]           = isset($deal->result->$happiCHAThappiAPPPaymentStatus) ? $deal->result->$happiCHAThappiAPPPaymentStatus : "";

            $preDealData[$happiAPPMakePaymentLink]           = isset($deal->result->$happiAPPMakePaymentLink) ? $deal->result->$happiAPPMakePaymentLink : "";
            $preDealData[$happiAPPPaymentOrderLink]           = isset($deal->result->$happiAPPPaymentOrderLink) ? $deal->result->$happiAPPPaymentOrderLink : "";
            $preDealData[$happiAPPPaymentStatus]           = isset($deal->result->$happiAPPPaymentStatus) ? $deal->result->$happiAPPPaymentStatus : "";

            $preDealData[$happiTALKMakePaymentLink]           = isset($deal->result->$happiTALKMakePaymentLink) ? $deal->result->$happiTALKMakePaymentLink : "";
            $preDealData[$happiTALKPaymentOrderLink]           = isset($deal->result->$happiTALKPaymentOrderLink) ? $deal->result->$happiTALKPaymentOrderLink : "";
            $preDealData[$happiTALKPaymentStatus]           = isset($deal->result->$happiTALKPaymentStatus) ? $deal->result->$happiTALKPaymentStatus : "";
        }
        return $preDealData;
    }

    public function getPreviousProducts($id)
    {
        $method = 'crm.deal.productrows.get.json';
        $response = Http::get($this->webhook . $method, [
            "id" => $id
        ]);
        return json_decode($response->body());
    }

    public function addOrUpdateContactForDeal($dealId, $user)
    {
        $deal = $this->getDeal($dealId);

        /** If email is there */
        if (isset($user['email'])) {
            /** If contact exits in bitrix */
            $contactId = $this->dealVariables['contactId'];
            if (!is_null($deal->result) && ($deal->result->$contactId) && ($deal->result->$contactId != "" || $deal->result->$contactId != "0")) {
                $method = 'crm.contact.update.json';
                $response = Http::get($this->webhook . $method, [
                    "id" => $deal->result->$contactId,
                    $this->contactAttributes['name'] => isset($user['nickname']) ? $user['nickname'] : "",
                    $this->contactAttributes['email'] => isset($user['email']) ? $user['email'] : "",
                    $this->contactAttributes['mobile'] => isset($user['mobile']) ? $user['mobile'] : "",
                ]);
            } else {
                $method = 'crm.contact.add.json';
                $response = Http::get($this->webhook . $method, [
                    $this->contactAttributes['name'] => isset($user['nickname']) ? $user['nickname'] : "",
                    $this->contactAttributes['email'] => isset($user['email']) ? $user['email'] : "",
                    $this->contactAttributes['mobile'] => isset($user['mobile']) ? $user['mobile'] : "",
                ]);
            }

            /** Return the Bitrix response. */
            return json_decode($response->body());
        }
    }

    public function addProductDeal($id, $datas)
    {
        /**
         * datas=> array(['package_id'=>1, 'price'=>1, 'quantity'=>1])
         */

        /** Defining method to add product deal */
        $method = 'crm.deal.productrows.set.json';
        $previousProducts = $this->getPreviousProducts($id);
        $prev_products = [];

        foreach ($previousProducts->result as $product) {
            $packageId = $this->dealProductAttributes['packageId'];
            $packagePrice = $this->dealProductAttributes['price'];
            array_push(
                $prev_products,
                [
                    'PRODUCT_ID' => $product->$packageId,
                    'PRICE' => $product->$packagePrice,
                    'QUANTITY' => 1
                ]
            );
        }

        $datas = $this->preProcessProductDeal($datas);
        $datas = array_merge($datas, $prev_products);

        $response = Http::get($this->webhook . $method, [
            "id" => $id,
            "rows" => $datas,
        ]);

        /** Return the Bitrix response. */
        return json_decode($response->body());
    }

    public function getExistingCustomerSupportID($dealId)
    {
        $method = 'crm.deal.list.json';

        $response = Http::post($this->webhook . $method, [
            "filter" => [
                "TITLE" => $dealId,
            ],
            "select" => ["ID", "TITLE"],
        ]);

        $res = json_decode($response->body());
        $custormerSupportDealId = ((!is_null($res->result)) && count($res->result) > 0) ? $res->result[0]->ID : 0;
        return $custormerSupportDealId;
    }
}
