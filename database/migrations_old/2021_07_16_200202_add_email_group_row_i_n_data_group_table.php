<?php

use App\Models\DataContent;
use App\Models\DataGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailGroupRowINDataGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dataGroup = DataGroup::create(['name' => 'email-orientation']);
        $dataContent = DataContent::create(['title' => 'body', 'data_group_id' => $dataGroup->id, 'content' => 'Hello [[name]],<br>
<br>
Welcome to HappiMynd!<br>
<br>
Congratulations! You are one step closer to start your journey towards holistic Mental Wellness & Emotional Wellbeing. To make things extra special for you, <b>[[company]]</b>  has made HappiMynd Services <b>[[packages]]</b> available to you for next 90 days.<br>
<br>
Here are some important details which will help you Start this journey,
<br>
•	Your HappiMynd Code is <b>[[token]]</b>.
<br>
•	Also, starting today, we will send you a series of exclusive emails with amazing nuggets to help strengthen your Emotional Wellbeing & help get the most out of this journey.
<br>
•	Please use the attached user process note in case you need any assistance on understanding the process around accessing the tools.
<br>
•	Here are the steps for accessing <b>HappiLIFE Screening</b>:
<br>
a.  Click on <a href="https://happimynd.com/signup">Sign up</a><br>
b.	Click “Organization/Institution”<br>
c.	Select organization <b>[[company]]</b><br>
d.	Use your unique <b>HappiMynd Code</b>. This code can be used only once.<br>
e.	Follow the instructions & enjoy your “HappiLIFE Screening”<br>
<br>
NOTE –<br>
<br>
1.All your details & data are <b>completely Confidential & Secure</b>. None of your individual level details will be shared with anyone in your organization or outside of it.<br>
<br>
2.HappiCHAT includes access to “Personalized CHAT Room” & “Curated Library”.<br>
<br>
<br>
In case you would have any queries, feel free to connect with us at <span style="color:blue;">support@happimynd.com</span><br>
<br>
<br>
<br>
Be Happi!<br>
<br>
Team HappiMynd ']);
        $dataContent = DataContent::create(['title' => 'subject', 'data_group_id' => $dataGroup->id, 'content' => 'Welcome to the journey of Holistic Mental Wellbeing!']);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dataGroup = DataGroup::where('name', 'email-orientation')->with('content')->first();
        if ($dataGroup) {
            if ($dataGroup->content->count() > 0) {
                foreach ($dataGroup->content as $content) {
                    $content->forceDelete();
                }
            }
            $dataGroup->forceDelete();
        }
    }
}
