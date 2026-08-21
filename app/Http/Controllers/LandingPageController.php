<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\OurTeam;
use App\Models\DataGroup;
use App\Models\EditButton;
use App\Models\OrganizationLogo;
use App\Models\PostCategory;
use App\Models\OrganizationPageData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    //
    public function happispaceform(){
        $dataContents = DataGroup::with(['content' => function($query){
            $query->where('title', 'happySpace_cdnlink');
        }])->where('name', 'landing_page')->first();
        if(count($dataContents->content) >= 1){
            $happySpace_cdnlink = $dataContents->content[0]->content;
        }else{
            $happySpace_cdnlink = '';
        }
        return view('Frontend/happispaceform/happispace_form')->with('happySpace_cdnlink', $happySpace_cdnlink);
    }

    public function ourTeam(){
        $founders=OurTeam::where('category','founders')->orderBy('preference')->get();
        $experts=OurTeam::where('category','experts')->orderBy('preference')->get();
        $psychologists=OurTeam::where('category','psychologists')->orderBy('preference')->get();
        return view('Frontend/ourteam/ourteam')->with('founders',$founders)->with('experts', $experts)->with('psychologists',$psychologists);
    }

    public function freeBlog(){
        $posts= PostCategory::with(['post'=> function($query){
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1
                ]);
        }])->latest()->get();

        if(Auth::check()){
            if($this->getPackage()){

                $posts= PostCategory::with(['post'=> function($query){
                    $query->where([
                        'publish_status' => 1
                        ])->orderBy('restricted_content', 'desc');
                }])->latest()->get();
            }
        }

        $blogs = '';
        $videos = '';
        $audios = '';
        $featured = Post::where('featured', 1)->first();
        foreach($posts as $postItem){
            if($postItem->id == 1){
                if(count($postItem->post) > 0){
                    $blogs = $postItem->post;

                }
            }else if($postItem->id == 2){
                if(count($postItem->post) > 0){
                    $videos = $postItem->post;

                }
            }else{
                if(count($postItem->post) > 0){
                    $audios = $postItem->post;

                }
            }
        }

        return view('Frontend/blog/blog')
        ->with([
            'blogs' => $blogs,
            'videos' => $videos,
            'audios' => $audios,
            'featured' => $featured
            ]);
    }

    public function readFreeBlog($slug)
    {
        $relatedArticle = '';
        $post = Post::where('slug', $slug)->first();
        // free user
        if(!$post){
            abort(404);
        }
        $relatedPosts = PostCategory::with(['post' => function($query) use($slug, $post) {
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1,
                'post_category_id' => $post->post_category_id
            ])->where('id', '!=', $post->id);

        }
        ])->where('id', $post->post_category_id)->get();

        if(Auth::check()){
            if($this->getPackage()){
                $relatedPosts = PostCategory::with(['post' => function($query) use($slug, $post) {
                    $query->where([
                        'publish_status' => 1,
                        'post_category_id' => $post->post_category_id
                    ])->where('id', '!=', $post->id)->orderBy('restricted_content', 'desc');
                }
                ])->where('id', $post->post_category_id)->get();
            }

        }

        if(count($relatedPosts[0]->post) >= 1){

            $relatedArticle = ($relatedPosts[0]->post)->splice(0,3);

        }
       return view('Frontend/blog/read_blog')->with('post', $post)->with('relatedArticle', $relatedArticle);
    }

    public function organization(){
        $organizationFaqs = DataGroup::with('content')->where('name', 'faqs-organization')->first();
        $organizations=OrganizationPageData::all();
        $logos=OrganizationLogo::all();
        $organisation_buttons = EditButton::where('page_name', 'organisation')->get();
        foreach($organizations as $organization){
            if($organization->id!=1){
                $desc=html_entity_decode(strip_tags($organization->description));
                $lines = explode('*',$desc);
                $organization['lines']=$lines;
            }
            else{
                $desc=html_entity_decode(strip_tags($organization->description));
                $organization['description']=$desc;
            }
        }
        return view('Frontend/organisation/organisation')->with('organizationFaqs', $organizationFaqs)->with('organizations',$organizations)->with('logos',$logos)->with('organisation_buttons', $organisation_buttons);
    }

    public function allBlog(Request $request, $slug)
    {
        $posts = PostCategory::with(['post' => function($query) {
            $query->where([
                'restricted_content' => 0,
                'publish_status' => 1,
            ]);
        }
        ])->where('name', $slug)->first();

        if(Auth::check()){
            if($this->getPackage())
            $posts = PostCategory::with(['post' => function($query) {
                $query->where([
                    'publish_status' => 1,
                ])->orderBy('restricted_content', 'desc');
            }
            ])->where('name', $slug)->first();
        }


       return view('Frontend/blog/all_blog')->with('posts', $posts);
    }

    public function getPackage(){
        $bundles = Auth()->user()->bundleStatus()->NotExpired()->get();
        if(!empty($bundles)){
            foreach ($bundles as $bundle){
                if($bundle->valid){
                    $packageArray =  explode('+', $bundle->plans->package->name );
                    if ($bundle->plans->package_id == 'HappiAPP' or in_array('HappiAPP', $packageArray))
                    return true;
                }
            }
        }
        return false;
    }

}
