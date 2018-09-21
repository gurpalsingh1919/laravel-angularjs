<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Common;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\PageSection;
use App\TemplateImages;
use Auth;
use Config;
use Hash;
use Redirect;
use Session;
use DB;
use App\ThirdParty;
use App\EbookPopUpImages;
use App\http\Controllers\Authenticate;
use App\PageDetail;
use App\Myfunnel;
use App\Template;
use App\Autoresponder;
use App\Ebookuser;
use App\Uniquehit;

class TemplateController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
      //  $this->middleware('jwt.auth');
    }

   
    public function editTemplateDetail(Request $request)
    { 
    	$inputs = $request->input();
    	$page_detail_id = $inputs['page_detail_id'];
    	$tempslug = $inputs['temp_slug'];
    	$tempId  = Template::whereRaw('temp_code = "'.$tempslug.'"')->get();
    	$templateId =  $tempId->toArray();
    	$template_id = $templateId[0]['id'];
    	//$user_id=1;
    	if($page_detail_id !='' && $page_detail_id !='0')
    	{
    	//*********************** main page detail Data **************************//
    	$pageSectionData = PageSection::whereRaw('template_id = "'.$template_id.'" and page_detail_id= "'.$page_detail_id.'" and status = 1')->get();
    	$pageSection =  $pageSectionData->toArray();
    	$jsondata=array();
    	$finalcatData=array();
    	$showHide=array();
    	foreach($pageSection as $detail)
    	{
    		$detailArr['option_id'] = $detail['option_id'];
    		$showHide='';
    		if($detail['option_id'] =='3')
    		{
    			//$detailArr['value'] = json_decode($detail['value']);
    			$jsondata=json_decode($detail['value']);
    			//echo "<pre>";print_r($jsondata);die;
    		}
    		else
    		{
    			$detailArr['value'] = json_decode($detail['value']);
    			$finalcatData[] = $detailArr;
    		}
    	}
	    	//print_r($page_detail_id); die;
	    	//******************** Template Images *************************//
	    	$finalImageData='';
	    	$tempImages=TemplateImages::WhereRaw('template_id = "'.$template_id.'" and status=1 and is_deleted = 0')->whereIn('is_default', [1, 0])->get();
	    	$imagestemplate =  $tempImages->toArray();
	    	foreach($imagestemplate as $imagess)
	    	{
	    		$ImageArr['id'] = $imagess['id'];
	    		$ImageArr['file_name'] = $imagess['file_name'];
	    		$ImageArr['image_type'] = $imagess['image_type'];
	    		$finalImageData[] = $ImageArr;
	    	}
	    	//******************** Get ebook Images  *************************//
	    	$ebooksAllImages = EbookPopUpImages::WhereRaw('template_id = "'.$template_id.'" and status= 1')->select('id', 'file_name', 'user_id','is_default')->get();
	    	$ebookformimg=$ebooksAllImages->toArray();
	    	//************** Left Data*******************************//
	    	$templateLeftArr=$this->templateLeftPanelData($template_id);
	    	//echo "<pre>";print_r($jsondata);die;
	    	$status = 'success';
	    	$finalArr = array(
	    			'rightArr'=>$finalcatData,
	    			'styleArr'=>$jsondata,
	    			'templateImages'=>$finalImageData,
	    			'ebookImages'=>$ebookformimg,
	    			'template_id'=>$template_id,
	    	        'leftArr'=>$templateLeftArr);
    	}
    	else 
    	{
    		$status = 'fail';
    		$finalArr = array('message'=>"Token expired please login");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    public function templateLeftPanelData($template_id)
    {
		    switch ($template_id)
		  	{
			    case 1:
			    	$templateArr[] = array(
			    			'Header' => array(
			    					'color_1/header_bkcolor'=>'Header Background Color',
			    					'text_1/header_title' => 'Heder Title',
			    					'logo_1/header_image' => 'Header Image'
			    			),
			    			'Banner' => array(
			    					'color_2/banner_bkcolor'=>'Background Color',
			    					'text_2/banner_text1'=>'Banner Title',
			    					'text_3/banner_text2'=>'Banner Subtitle',
			    					'logo_2/banner_image'=>'Banner Image',
			    					'text_4/banner_text3'=>'Banner Text',
			    					'link_1/banner_link'=>'Call to Action',
			    					'CallButton'=> array(
			    						'color_3/callbutton_bkcolor'=>'Background Color',
			    						'text_5/banner_call_to_action'=>'Title',
			    					),
			    			),
			    			'Video' => array(
			    					'text_6/vid_title'=>'Video Title',
			    					'text_7/vid_subtitle'=>'Video Subtitle',
			    					'video_1/vid_url'=>'Video Link',
			    					'color_4/video_bkcolor'=>'Video Background Color',
			    					'link_2/vid_link'=>'Call To Action',
			    					'CallButton'=> array(
			    						'color_5/video_callbutton_bkcolor'=>'Background Color',
			    						'text_8/video_call_to_action'=>'Title',
			    					),
			    			),
			    			'Details' => array(
			    					'color_6/detail_bkcolor'=>'Background Color',
			    					'text_9/details_title'=>'Title',
			    					'text_10/details_subtitle'=>'Subtitle',
			    					'text_11/details_text'=>' Text',
			    					'link_3/details_link'=>'Call to Action',
			    					'CallButton'=> array(
			    						'color_7/details_callbutton_bkcolor'=>'Background Color',
			    						'text_12/details_call_to_action'=>'Title',
			    					),
			    			),
			    			'Copyrights' => array(
			    					'color_8/copyright_bkcolor'=>'Footer Background Color',
			    					'text_13/copyright_title'=>'Footer Title',
			    			), 
			    	);
			        break;
			    case 2:
			    	$templateArr[] = array(
			    			'Header' => array(
			    					'text_1/header_title' => 'Header Title',
			    					'logo_1/header_image' => 'Header Image'
			    			),
			    			'Banner' => array(
			    					'text_2/timer_heading'=>' Title',
			    			),
			    			'Video' => array(
			    					'video_1/vid_url'=>'Video Link'
			    			),
			    			'Details' => array(
			    					'text_3/details_title'=>'Title',
			    					'text_4/details_subtitle'=>'Subtitle',
			    					'link_1/details_link'=>'Call to Action',
			    				    'CallButton'=> array(
			    						'color_1/details_callbutton_bkcolor'=>'Background Color',
			    						'text_5/details_call_to_action'=>'Title',
			    					),
			    			),
			    			'Copyrights' => array(
			    					'color_2/copyright_bkcolor'=>'Footer Background Color',
			    					'text_6/copyright_title'=>'Footer Title'
			    			),	 
			    	);
			        break;
			        case 3:
			        $templateArr[] = array(
			        		'Header' => array(
			        		'color_1/header_bkcolor'=>'Header Background Color',
			        		'logo_1/header_image' => 'Header Image'
			        				
			        		),
			        		'Banner' => array(
			        				'color_2/banner_bkcolor'=>'Banner Background Color',
			        				'logo_2/banner_image'=>'Banner Image',
			        				'text_1/banner_text1'=>'Banner Title',
			        				'text_2/banner_text2'=>'Banner Subtitle',
			        				'text_3/banner_text3'=>'Banner Text3',
			        				'url_1/social_icon_link1'=>'SocialLink #1',
			        				'url_2/social_icon_link2'=>'SocialLink #2',
			        				'url_3/social_icon_link3'=>'SocialLink #3',
			        				'url_4/social_icon_link4'=>'SocialLink #4'

			        		),
			        		'Testimonial' => array(
			        				'color_3/testimonial_bg'=>'Background Color', 
			        				'text_4/testimonial1_content'=>'Testimonial #1 Text',
			        				'text_5/testimonial1_author_name'=>'Author #1 Name',
			        				'text_6/testimonial1_author_designation'=>'Author #1 Designation',
			        				'logo_3/testimonial1_image'=>'Testimonial #1 Image',
			        				'text_7/testimonial2_content'=>'Testimonial #2 Text',
			        				'text_8/testimonial2_author_name'=>'Author #2 Name',
			        				'text_9/testimonial2_author_designation'=>'Author #2 Designation',
			        				'logo_4/testimonial2_image'=>'Testimonial #2 Image',
			        				'color_4/testimonial_call_to_action'=>'Button Background',
			        				'link_1/details_link'=>'Call to Action',
			        				'CallButton'=> array(
			        						'color_5/callbutton_bkcolor'=>'Background Color',
			        						'text_10/details_call_to_action'=>'Title',
			        				),
			        		),
			        		'Copyrights' => array(
			        				'color_6/footer_bg'=>'Footer Background Color',
			        				'text_11/copyright_title'=>'Footer Title',
			        		),
			        );
			        break;
			        case 12:
			        	$templateArr[] = array(
			        	'Header' => array(
				        	'color_1/header_bkcolor'=>'Header Background Color',
				        	'logo_1/header_logo' => 'Header Logo',
				        	'logo_2/header_image' => 'Header Image'
			        			),
	        			'Banner' => array(
	        				'color_2/body_bkcolor'=>'Body Background',
		        			'text_1/banner_text1'=>'Banner Title',
		        			'text_2/banner_text2'=>'Banner Text',
	        					),
	        			'Details' => array(
		        			'color_3/detail_bkcolor'=>'Details Background Color',
		        			'tab_1/details_link'=>'Proceed To Checkout Button Link',
			        			'CallButton'=> array(
			        					'color_4/callbutton_bkcolor'=>'Background Color',
			        					'text_3/details_call_to_action'=>'Title',
			        					),
		        			'logo_3/module1_image'=>'Module-1 Image',
			        			'Module1'=> array(
			        				'text_4/module1_title'=>'Module #1 Title',
				        			'text_5/module1_text'=>'Module #1 Text',
				        				),
			        		'logo_4/module2_image'=>'Module #2 Image',
				        		'Module2'=> array(
				        			'text_6/module2_title'=>'Module #2 Title',
				        			'text_7/module2_text'=>'Module #2 Text',
				        			),
				        	'logo_5/module3_image'=>'Module #3 Image',
				        		'Module3'=> array(
				        			'text_8/module3_title'=>'Module #3 Title',
				        			'text_9/module3_text'=>'Module #3 Text',
				        			),
				        	'logo_6/module4_image'=>'Module #4 Image',
				        		'Module4'=> array(
				        			'text_10/module4_title'=>'Module #4 Title',
				        			'text_11/module4_text'=>'Module #4 Text',
				        			),
				        	'logo_7/module5_image'=>'Module #5 Image',
					        	'Module5'=> array(
			        			'text_12/module5_title'=>'Module #5 Title',
			        			'text_13/module5_text'=>'Module #5 Text',
			        			),
			        		'logo_8/module6_image'=>'Module #6 Image',
				        		'Module6'=> array(
			        			'text_14/module6_title'=>'Module #6 Title',
			        			'text_15/module6_text'=>'Module #6 Text',
			        			),
	        			   'logo_9/module_block1_image'=>'Module block #1 Image',
		        			   'ModulesBlocks'=> array(
			        			'text_16/module_block1_text1'=>'Module block #1 Text',
			        			'text_17/module_block1_title'=>'Module block #1 Title',
			        			'text_18/module_block1_price'=>'Module block #1 price',
			        			'text_19/module_block1_price_text'=>'Module block #1 Price Text',
			        			),
		        		'logo_10/guarantee1_block_image'=>'Guarantee #1 Block Image',
			        		'GuaranteeFirstBlocks'=> array(		
			        			'text_20/guarantee1_block_title'=>'Guarantee #1 Block Title',
			        			'color_5/guarantee1_headerbk'=>'Guarantee #1 Header Color',
			        			'color_6/guarantee1_bodybk'=>'Guarantee #1 Body Color',		        			
			        			'text_21/guarantee1_block_text'=>'Guarantee #1 Block Text',
			        			),
		        		'logo_11/bonus1_right_image'=>'Bonus #1 Right Image',
		        		'logo_12/bonus1_left_image'=>'Bonus #1 Left Image',
		        			'BonusFirstBlocks'=> array(
		        				'text_22/bonus1_title'=>'Bonus #1 Title',
			        			'text_23/bonus1_subtitle'=>'Bonus #1 SubTitle',
			        			'color_7/bonus1_bkcolor'=>'Bonus #1 Background',
			        			'text_24/bonus1_image_value'=>'Bonus #1 Image Value',
			        			'text_25/bonus1_text_Title'=>'Bonus #1 Text Title',
			        			'text_26/bonus1_text'=>'Bonus #1 Text',
			        			),
			        	'logo_13/bonus2_right_image'=>'Bonus #2 Right Image',
			        	'logo_14/bonus2_left_image'=>'Bonus #2 Image',
				        		'BonusSecondBlocks'=> array(
			        			'text_27/bonus2_title'=>'Bonus #2 Title',
			        			'text_28/bonus2_subtitle'=>'Bonus #2 SubTitle',
			        			'color_8/bonus2_bkcolor'=>'Bonus #2 Background',
			        			'text_29/bonus2_image_value'=>'Bonus #2 Image Value',
			        			'text_30/bonus2_text_Title'=>'Bonus #2 Text Title',
			        			'text_31/bonus2_text'=>'Bonus #2 Text',
			        			'text_32/bonus2_image_below_text'=>'Bonus #2 Image Below Text',
			        			),
			        		'logo_15/bonus3_right_image'=>'Bonus #3 Right Image',
			        		'logo_16/bonus3_left_image'=>'Bonus #3 Left Image',
				        		'BonusThirdBlocks'=> array(
			        			'text_33/bonus3_title'=>'Bonus #3 Title',
			        			'text_34/bonus3_subtitle'=>'Bonus #3 SubTitle',
			        			'color_9/bonus3_bkcolor'=>'Bonus #3 Background',
			        			'text_35/bonus3_image_value'=>'Bonus #3 Image Value',
			        			'text_36/bonus3_text'=>'Bonus #3 Text',
			        			),
		        			'logo_17/bonus_detail_image'=>'Bonus Detail Image',
			        			'BonusDetailBlocks'=> array(
				        			'text_37/bonus_detail_title'=>'Bonus Detail Title',
				        			'text_38/bonus_detail_text'=>'Bonus Detail text',
				        			'text_39/bonus_detail_subtitle'=>'Bonus Detail SubTitle',
				        			'text_40/bonus_detail_address'=>'Bonus Detail Address',
			        			  	),
		        			'logo_18/guarantee2_image'=>'Guarantee #2 Image',
				        		'GuaranteeSecondBlocks'=> array(
				        			'text_41/guarantee2_block_text'=>'Guarantee #2 Block Text',
				        			),
	        				'text_42/signup_title'=>'Signup Title',
	        				'color_10/signup_title_bkcolor'=>'Signup Title Background Color',
	        				'logo_19/signup_left_bottom_image'=>'Left Bottom Image',
	        				'tab_2/signup_left_callbutton_link'=>'Left Button Link',
			        			'SignupLeft'=> array(
			        					'text_43/signup_left_panel_title'=>'Left panel Title',
			        					'color_11/signup_left_panel_title_bkcolor'=>'Left panel Title Background Color',
			        					'color_12/signup_left_panel_bkcolor'=>'Left panel Background Color',
			        					'text_44/signup_left_panel_top_text'=>'Left panel Top Text',
			        					'text_45/signup_left_panel_value'=>'Left panel value',
			        					'text_46/signup_left_panel_bottom_text'=>'Left panel Bottom Text',
			        					'color_13/signup_left_callbutton_bkcolor'=>'Left Button Background Color',
		        				        'text_47/signup_left_call_to_action_title'=>'Left Button Title',
		        				        ),
		        				  'logo_20/signup_right_bottom_image'=>'Right Bottom Image',
		        				  'tab_3/signup_right_callbutton_link'=>'Right Button Link',
		        				        'SignupRight'=> array(
		        				        'text_48/signup_right_panel_title'=>'Right panel Title',
		        				        'color_14/signup_right_panel_title_bkcolor'=>'Right panel Title Background Color',
		        				        'color_15/signup_right_panel_bkcolor'=>'Right panel Background Color',
		        				        'text_49/signup_right_panel_top_text'=>'Right panel Top Text',
		        				        'text_50/signup_right_panel_value'=>'Right panel value',
		        				        'text_51/signup_right_panel_bottom_text'=>'Right panel Bottom Text',
		        				        'color_16/signup_right_callbutton_bkcolor'=>'Right Button Background Color',
		        				        'text_52/signup_right_call_to_action_title'=>'Right Button Title',
			        					),
	        			),
	        			'Copyrights' => array(
        				'text_53/copyright_menu1'=>'Copyright Menu #1',
        				'text_54/copyright_menu2'=>'Copyright Menu #2',
        				'tab_4/copyright_menu1_link'=>'Copyright Menu #1 Link',
        				'tab_5/copyright_menu2_link'=>'Copyright Menu #2 Link',
	        			'text_55/copyright_title'=>'Footer Title',
	        			'text_56/copyright_text'=>'Footer Text',
	        			),
			        );
			        break;
			        case 13:
			        	$templateArr[] = array(
			        	'Header' => array(
				        	'color_1/header_bkcolor'=>'Header Background Color',
				        			),
        				'Video' => array(
        				'text_1/vid_title'=>'Video Title',
        				'logo_1/vid_url'=>'Video Link'
        						),
        				'OptinForm' => array(
        				'color_3/detail_bkcolor'=>'Background Color',
        				'text_2/details_text'=>'Text',
        				'text_3/detail_form_title'=>'Form Title',
        				/*'CallButton'=> array(
        				'color_4/details_callbutton_bkcolor'=>'Button Background Color',
        				'text_5/details_call_to_action_title'=>'Button Title',
        						),*/
			        			),					
			        	);
			        	break;
			        	case 14:
			        		$templateArr[] = array(
			        		'Header' => array(
			        		'color_1/header_bkcolor'=>'Header Background Color',
			        		'logo_1/header_image' => 'Header Logo',
			        		'logo_2/video1_image'=>'Video #1 Thumbnail',
			        		'tab_1/video1_link'=>'Video #1 Link',
				        		'VideoFirst' => array(
				        		'text_1/video1_title'=>'Video #1 Title',
				        		'text_2/video1_subtitle'=>'Video #1 Subtitle',
				        			),
				        	'logo_3/video2_image'=>'Video #2 Thumbnail',
				        	'tab_2/video2_link'=>'Video #2 Link',
				        		'VideoSecond' => array(
				        		'text_3/video2_title'=>'Video #2 Title',
				        		'text_4/video2_subtitle'=>'Video #2 Subtitle',
				        			),
				        	'logo_4/video3_image'=>'Video #3 Thumbnail',
				        	'tab_3/video3_link'=>'Video #3 Link',
				        		'VideoThird' => array(
					        		'text_5/video3_title'=>'Video #3 Title',
					        		'text_6/video3_subtitle'=>'Video #3 Subtitle',
					        			),
					        'logo_5/video4_image'=>'Video #4 Thumbnail',
					        'tab_4/video4_link'=>'Video #4 Link',
				        		'VideoFourth' => array(
					        		'text_7/video4_title'=>'Video #4 Title',
					        		'text_8/video4_subtitle'=>'Video #4 Subtitle',
					        				),
					        'color_2/body_bkcolor'=>'Body Background',				
					        		),
	        				'Video' => array(
	        				'video_1/vid_url'=>'Video Link',
	        				'text_9/video_below_text'=>'Video Below Text',		
	        						),
        					'Details' => array(
        					'color_3/detail_bkcolor'=>'Background Color',
        					'text_10/details_title'=>'Detail Block Title',
        					'text_11/details_text1'=>'Detail text #1',
        					'text_12/details_subtitle'=>'Detail Block Subtitle',
        					'text_13/details_text2'=>'Detail text #2',
        					'logo_6/detail_rightsidebar1_image'=>'Right Sidebar First Image',
        					'logo_7/detail_rightsidebar2_image'=>'Right Sidebar Second Image',
        					'text_14/detail_rightsidebar_title'=>'Right Sidebar Title',
        					'tab_5/details_link'=>'Download Button Link',
	        					'CallButton'=> array(
	        							'color_4/callbutton_bkcolor'=>'Background Color',
	        							'text_15/details_call_to_action'=>'Button Title',
	        							),
	        				'text_16/detail_fb_title'=>'Detail Fb Block Title',
	        				'text_17/detail_comment_title'=>'Detail Comment Title',
	        				'text_18/detail_comment_text'=>'Detail Comment Text',
        						),
        					'Copyrights' => array(
        						'text_19/copyright_menu1'=>'Copyright Menu #1',
        						'text_20/copyright_menu2'=>'Copyright Menu #2',
        						'tab_6/copyright_menu1_link'=>'Copyright Menu #1 Link',
        						'tab_7/copyright_menu2_link'=>'Copyright Menu #2 Link',
        						'text_21/copyright_title'=>'Footer Title',
        						'text_22/copyright_text'=>'Footer Text',
        							),
			        			);
			        		break; 
				        	case 15:
				        		$templateArr[] = array(
				        		'Header' => array(
				        		'color_1/header_bkcolor'=>'Header Background Color',
				        		'logo_1/header_image' => 'Header Image'
				        		),
				        		
				        		'Video' => array(
				        		'color_2/banner_bkcolor'=>'Video Block Background Color',
				        		'text_1/vid_title'=>'Video Title',
				        		'text_2/vid_subtitle'=>'Video Subtitle',
				        		'video_1/vid_url'=>'Video Link',
				        		'text_3/video_text'=>'Video Below Right Text',
				        		),
		        				'Details' => array(
		        				'color_3/detail_bkcolor'=>'Body Background',
		        				'text_4/details_title'=>'Detail Text #1',
		        				'tab_1/details_link'=>'Add To Cart Button Link',
		        					'CallButton'=> array(
		        						'color_4/callbutton_bkcolor'=>'Button Background Color',
		        						'text_5/details_call_to_action'=>'Button Title',
		        					),
		        				'text_6/detail_text1'=>'Detail Text #2',
		    					),
		    					'Copyrights' => array(
		    					'text_7/copyright_title'=>'Footer Title',
		    					'text_8/copyright_text'=>'Footer Text',
		    					),
				        		);
				        		break;
			 }
			 return $templateArr;
    }
    //************** Functiona for Image Upload ******************************//
    public  function imageUpload()
    { 	
    	//$template_id=1;
    	$template_id =$_POST['templateid'];
    	$template_slug =$_POST['templateslug'];
    	//$imageType =$_POST['imagetype'];
    	$userId=1;
    	$template_images = new TemplateImages;
    	$origionalname = $_FILES['file']['name'];
    	$origionalnamespart=explode('.', $origionalname);
    	$tempname= $_FILES['file']['tmp_name'];
    	$filename = $origionalnamespart[0].'_'.time().'.'.pathinfo($origionalname, PATHINFO_EXTENSION);
    	if(isset($_FILES['file']['name']) && strlen($_FILES['file']['name']))
    	{
    	//	$target = 'public/assets/admin/facebook-tab/images/'.$filename;
    		$target = 'public/assets/template/'.$template_slug.'/images/'.$filename;
    		
    		$template_name = explode('.', $filename);
    		if (!move_uploaded_file( $tempname, $target))
    		{
    			$status = 'error';
    			
    			$finalArr = array('message_text'=>'An error occurred in uploading file.');
    			//$this->sendResponse($status,$finalArr);
    			//Yii::$app->Messages->status_message($status,$finalArr);
    			$result = Common::sendRequest($status,$finalArr);
    			return $result;
    		}
    		else
    		{
    			$template_images->file_name	= $filename;
    			$template_images->original_name = $_FILES['file']['name'];
    			$template_images->file_size = $_FILES['file']['size'];
    			$template_images->file_type = $_FILES['file']['type'];
    			$template_images->table_id = $userId;
    			$template_images->table_reference = 'tbl_users';
    			$template_images->created_at = date('Y-m-d H:i:s');
    			$template_images->user_id = $userId;
    			$template_images->template_id = $template_id;
    			$template_images->is_default = 0;
    			$template_images->image_type = 'template';

    			if($template_images->save()){
    				$last_Id = $template_images->id;
    				$status = 'success';
    				$finalArr = array('name'=>$filename,'id'=>$last_Id,'message_text'=>'File uploaded successfully.','type'=>'template');
    				//$this->sendResponse($status,$finalArr);
    				$result = Common::sendRequest($status,$finalArr);
    				return $result;
    			}
    		}
    		
    	}
    }
    //************** Function to Delete Images ******************************//
   
    public function deleteImage(Request $request){
    	$inputs=$request->input();
    	$imgid = $inputs['imgid'];
    	$templateId = $inputs['tempid'];
    	if($imgid)
    	{
    		//$tempimage = TemplateImages::WhereRaw('template_id = "'.$templateId.'" and id = "'.$imgid.'"')->get();
    		$tempimage = TemplateImages::find($imgid);
    		$fileName =  $tempimage['file_name'];
    		//$tempimage->delete();
    		$tempimage->is_deleted=1;
    		//$tempimage->save();
    		//$path = 'public/assets/admin/facebook-tab/images/'.$fileName;
    		//unlink($path);
    		if($tempimage->save()){
    		$status="success";
    		$message="Image deleted successfully";
    		$result = Common::sendRequest($status,$message);
    		return $result;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    //************** Functiona for Background Image Upload ******************************//
    public  function backgroundimageupload()
    {
    	$template_id =$_POST['templateid'];
    	$template_slug =$_POST['templateslug'];
    	$userId=1;
    	$template_images = new TemplateImages;
    	$origionalname = $_FILES['file']['name'];
    	$origionalnamespart=explode('.', $origionalname);
    	$tempname= $_FILES['file']['tmp_name']; 
    	$filename = $origionalnamespart[0].'_'.time().'.'.pathinfo($origionalname, PATHINFO_EXTENSION);
    	if(isset($_FILES['file']['name']) && strlen($_FILES['file']['name']))
    	{
    		$target = 'public/assets/template/'.$template_slug.'/images/'.$filename;
    		list($width, $height) = getimagesize($tempname);
    		$min_width = "1200";
			$min_height = "1050";
    		$template_name = explode('.', $filename);
    		/*if($width < $min_width || $height < $min_height )
    		{ 
    			$status = 'error';
    			$finalArr = array('message_text'=>'Please upload image with mentioned dimensions.');
    			$result = Common::sendRequest($status,$finalArr);
    			return $result;
    		}
    		else {*/
    			
    			if (!move_uploaded_file( $tempname, $target))
    			{
	    			$status = 'error';
	    			$finalArr = array('message_text'=>'An error occurred in uploading file.');
	    			$result = Common::sendRequest($status,$finalArr);
	    			return $result;
    			}
	    		else
	    		{
	    			$template_images->file_name	= $filename;
	    			$template_images->original_name = $_FILES['file']['name'];
	    			$template_images->file_size = $_FILES['file']['size'];
	    			$template_images->file_type = $_FILES['file']['type'];
	    			$template_images->table_id = $userId;
	    			$template_images->table_reference = 'tbl_users';
	    			$template_images->created_at = date('Y-m-d H:i:s');
	    			$template_images->user_id = $userId;
	    			$template_images->template_id = $template_id;
	    			$template_images->is_default = 0;
	    			$template_images->image_type = 'background';
	    
	    			if($template_images->save()){
	    				$last_Id = $template_images->id;
	    				$status = 'success';
	    				$finalArr = array('name'=>$filename,'id'=>$last_Id,'message_text'=>'File uploaded successfully.','type'=>'background');
	    				//$this->sendResponse($status,$finalArr);
	    				$result = Common::sendRequest($status,$finalArr);
	    				return $result;
	    			}
    		     }
    		  // }
    	}
    }
    
   
	//****************** Function upload ebook pop up images *****************//
  
   public function UploadEbookImage(){
  	 	$template_id =$_POST['templateid'];
    	$userId=1; 
    	$ebook_popup_images = new EbookPopUpImages;

    	$origionalname = $_FILES['file']['name'];
    	$origionalnamespart = explode('.', $origionalname);
    	$tempname = $_FILES['file']['tmp_name'];
    	$filename = $origionalnamespart[0].'_'.time().'.'.pathinfo($origionalname, PATHINFO_EXTENSION);
    	if(isset($_FILES['file']['name']) && strlen($_FILES['file']['name']))
    	{
    		//if (!file_exists('public/assets/admin/images/ebook-img/user_'.$userId)) {
    		//	mkdir('public/assets/admin/images/ebook-img/user_'.$userId, 0777, true);
    		//}
    		//$target = 'public/assets/admin/images/ebook-img/user_'.$userId.'/'.$filename;
    		$target = 'public/assets/admin/images/ebook-img/'.$filename;
    		$template_name = explode('.', $filename);
    		$file_size = $_FILES['file']['size'];
    		$maxsize = '5242880';
    		/*if($template_id=='')
    		{ 
    			$status = 'error';
    			$finalArr = array('message_text'=>'File too large. File must be less than 5 megabytes.');
    			//return false;
    		}*/
    		//******************** check height and width ***************//
    		if($file_size>$maxsize)
    		{
    			$status = 'error';
    			$finalArr = array('message_text'=>'File too large. File must be less than 5 megabytes.');
    			//return false;
    		}
    		else
    		{
    		if (!move_uploaded_file( $tempname, $target))
    		{
    			$status = 'error';
    			$finalArr = array('message_text'=>'An error occurred in uploading file.');	 
    		}
    		else
    		{
    			$ebook_popup_images->file_name	= $filename;
    			$ebook_popup_images->original_name = $_FILES['file']['name'];
    			$ebook_popup_images->file_size = $_FILES['file']['size'];
    			$ebook_popup_images->file_type = $_FILES['file']['type'];
    			$ebook_popup_images->created_at = date('Y-m-d H:i:s');
    			$ebook_popup_images->user_id = $userId;
    			$ebook_popup_images->is_default = 0;
    			$ebook_popup_images->template_id = $template_id;
    			if($ebook_popup_images->save())
    			{
    				$last_Id = $ebook_popup_images->id;
    				$status = 'success';
    				$finalArr = array('name'=>$filename,'is_default'=>$ebook_popup_images->is_default,'user_id'=>$userId,'id'=>$last_Id,'message_text'=>'File uploaded successfully.');
    			}
    		}
    		}
    		
    		$result = Common::sendRequest($status,$finalArr);
    		return $result;
    	}
    }
    
    //********************Delete Ebook Images********************************//
    public function ebookdeleteimage(Request $request){
    	$userId=1;
    	$inputs=$request->input();
    	$imgid = $inputs['imgid'];
    	$templateId = $inputs['tempid'];
    	if($imgid){
    		//$tempimage = TemplateImages::WhereRaw('template_id = "'.$templateId.'" and id = "'.$imgid.'"')->get();
    		$tempimage = EbookPopUpImages::find($imgid);    	
    		$fileName =  $tempimage['file_name'];
    		$tempimage->delete();
    		$path = 'public/assets/admin/images/ebook-img/'.$fileName;
    		//$path = 'public/assets/admin/facebook-tab/images/'.$fileName;
    		unlink($path);
    		$status="success";
    		$message="Image deleted successfully";
    		$result = Common::sendRequest($status,$message);
    		return $result;
    	}
    	else
    	{
    		return false;
    	}
    }
    
   
	   // Add New Custom Funnel
	public function updateTemplateDetail(Request $request)
	{
		$postdata=$request->input(); 
		$page_detail_id = $postdata[0]['page_detail_id'];
		$template_id = $postdata[0]['template_id'];
		//$thumb_image = $postdata[0]['thumbnail_image']; 
		//******************** Formating the right array data ******************//
		$output = array();
		if (! empty($postdata[0]['rightArr'])) {
			foreach ($postdata[0]['rightArr'] as $elem) {
				if (! empty($elem)) {
					foreach ($elem as $k => $v) {
						if($k!='option_id'){
							$output[$elem['option_id']][$k] = $v;
						}
					}
		
				}
			}
		}
		//************************** Add right array data  *****************************//
		foreach($output as $key=>$val){
			$jsondata =  json_encode($val);
			$affectedRows = PageSection::where('template_id', '=', $template_id)
			->where('option_id', '=', $key)
			->where('page_detail_id', '=', $page_detail_id)
			->update(['value' =>  $jsondata]);
		}
		//************************ All styles data *****************************************//
		if (! empty($postdata[0]['leftStyleArr']))
		{
			$val=$postdata[0]['leftStyleArr'];
			$affectedRows = PageSection::where('template_id', '=', $template_id)
			->where('option_id', '=', '3')
			->where('page_detail_id', '=', $page_detail_id)
			->update(['value' =>  json_encode($val)]);
			
		}
		if($page_detail_id !=''){
			$PageDetailData = PageDetail::find($page_detail_id);
			$affectedRows = Myfunnel::where('id', '=', $PageDetailData->myfunnel_id)
			->update(['updated_at' => date('Y-m-d H:i:s')]);
		}
		
		/*$nam = time();
		$ran = rand(10, 999);
		$name = $nam.$ran;
		$filedir = 'public/assets/admin/images/ebook-img/';
		$decoded = base64_decode(str_replace('data:image/png;base64,', '', $thumb_image));
		$filepath = $filedir.$name.".png";
		$res = file_put_contents($filepath, $decoded);*/
		
		
		$status = 'success';
		$finalArr = array('message'=>'Template Data has been Saved successfully!');
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}

	public function addNewFunnel(Request $request)
	{
		
		$template_id='1';
		$postdata=$request->input();
		if($postdata)
		{
			$token = $postdata['token'];
			$user_id =User::getId($token);
			$funnel_name=$postdata['funnel_name'];
			//$group_name=$postdata['group_name'];
			$group_name='';
			$templatedetail = new Myfunnel;
			$templatedetail->user_id = $user_id;
			$templatedetail->slug =time();
			$templatedetail->group_tag = $group_name;
			$templatedetail->funnel_name = $funnel_name;
			$templatedetail->is_deleted="0";
			$pstepId='';
			//Save Custom Funnel data into myFunnel table
			if($templatedetail->save())
			{
				$last_Id = $templatedetail->id;
				$slug = $templatedetail->slug;
				$funnel_name = $templatedetail->funnel_name;
				self::createDefaultFunnelSteps($last_Id,$user_id);
			}
			$status="Success";
			$finalArr=array("funnel_id"=>$last_Id,"funnel_name"=>$funnel_name,"slug"=>$slug,"step_id"=>$pstepId);
		}
		else 
		{
			$status="Fail";
			$finalArr=array("message"=>"Input not found");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		
	}
	public static function createDefaultFunnelSteps($funnel_id,$user_id)
	{
		$template1_slug='';
		$template2_slug='';
		for($i=1;$i<=6;$i++)
		{
			
			$PageDetail = new PageDetail;
			$PageDetail->Myfunnel_id=$funnel_id;
			$PageDetail->user_id=$user_id;
			$template_code='0';
			
			if($i=='1'){ $stepName="Optin"; $slug=$stepName.'-'.time();$template_id="13";$PageDetail->is_default="1";}
			else if($i=='2'){$stepName="Value Video 1"; $slug='Value-Video-1-'.time(); $template_id="14"; $template1_slug=$slug;$PageDetail->is_default="1"; }
			else if($i=='3'){$stepName="Value Video 2"; $slug='Value-Video-2-'.time(); $template_id="14";$template_code="5862";$template2_slug=$slug;$PageDetail->is_default="1"; }
			else if($i=='4'){$stepName="Value Video 3"; $slug='Value-Video-3-'.time(); $template_id="14"; $template_code="58621";$PageDetail->is_default="1";}
			else if($i=='5'){$stepName="Value Video 4"; $slug='Value-Video-4-'.time(); $template_id="15";$PageDetail->is_default="1";}
			else if($i=='6'){$stepName="Add To Cart";$slug='Add-To-Cart-'.time(); $template_id="12";$PageDetail->is_default="1"; }
			/*else if($i=='7'){$stepName="Custom Step";$slug='Custom-Step-'.time(); $template_id="0";$PageDetail->is_default="0"; }*/
			$PageDetail->name=$stepName;
			$PageDetail->slug=$slug;
			$PageDetail->industry_id="2";
			$PageDetail->template_id=$template_id;
			$PageDetail->description="This is default";
			$PageDetail->is_deleted="0";
			$PageDetail->sort_order=$i;
			if($PageDetail->save())
			{
				$pstepId=$PageDetail->id;
				$temp_id=$PageDetail->template_id;
				if($temp_id !='0')
				{
					
					self::addDefaultTemplateInSteps($pstepId,$temp_id,$template1_slug,$template2_slug,$template_code);
				}
				
			}
		}
		
		
	}
	public static function addDefaultTemplateInSteps($stepId,$templateID,$template1_slug,$template2_slug,$template_code)
	{
		$MyfunnelData = PageSection::whereRaw('template_id = "'.$templateID.'" and page_detail_id="0"')->orderBy('id', 'asc')->get();
		$funnelData =  $MyfunnelData->toArray();
		for($i=0;$i<count($funnelData);$i++)
		{
			$main_url=$_SERVER['HTTP_REFERER']."#/5862/";
			$pagesectiondatanew= new PageSection;
			$pagesectiondatanew->template_id=$funnelData[$i]['template_id'];
			$pagesectiondatanew->option_id=$funnelData[$i]['option_id'];
			$pagesectiondatanew->page_detail_id=$stepId;
		
			if($funnelData[$i]['option_id']=='21' && $template_code=='5862')
			{
				
				$video_url=$main_url.$template1_slug;
				$values=json_decode($funnelData[$i]['value']);
				$value = (array) $values;
				$value['video1_link']=$video_url;
				$value['video1_tab_value']='1';
				$funnelData[$i]['value']=json_encode($value);
			}
			if($funnelData[$i]['option_id']=='21' && $template_code=='58621')
			{
				$video_url1=$main_url.$template1_slug;
				$video_url2=$main_url.$template2_slug;
				$values=json_decode($funnelData[$i]['value']);
				$value = (array) $values;
				$value['video1_link']=$video_url1;
				$value['video2_link']=$video_url2;
				$value['video1_tab_value']='1';
				$value['video2_tab_value']='1';
				$funnelData[$i]['value']=json_encode($value);
			}
			$pagesectiondatanew->value=$funnelData[$i]['value'];
			$pagesectiondatanew->status='1';
			$pagesectiondatanew->created_at=date('Y-m-d H:i:s');
			$pagesectiondatanew->save();
	
		}
	}
	public function addFunnelStep(Request $request)
	{
		$inputs = $request->input();
		$token = $inputs['token'];
		$user_id =User::getId($token);
		$Myfunnel_id = $inputs['funnel_id'];
		$Myfunnelstep_name = $inputs['funnelStepName'];
		$Myfunnelstep_path = $inputs['path'];
	
		
		if(isset($Myfunnelstep_name))
		{
			$funnelsSteps = Myfunnel::find($Myfunnel_id)->pagedetail;
			$stepdata=$funnelsSteps->toArray();
			$no_ofStep=count($stepdata);
			$PageDetail = new PageDetail;
			$PageDetail->Myfunnel_id=$Myfunnel_id;
			$PageDetail->user_id=$user_id;
			$PageDetail->name=$Myfunnelstep_name;
			$PageDetail->slug=$Myfunnelstep_path;
			$PageDetail->industry_id="2";
			$PageDetail->template_id="0";
			$PageDetail->description="New Step";
			$PageDetail->is_default="0";
			$PageDetail->is_deleted="0";
			$PageDetail->sort_order=$no_ofStep +1;
			if($PageDetail->save())
			{
				$status="Success";
				$finalArr=array("id"=>$PageDetail->id,
						"sort_order"=>$PageDetail->sort_order,
						"stepName"=>$Myfunnelstep_name,
						"stepPath"=>$Myfunnelstep_path);
				
			}
			else 
			{
				$status="Fail";
				$finalArr=array("message"=>"An error occur while saving");
			}
		}
		else 
		{
			$status="Fail";
			$finalArr=array("message"=>"Input not found");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		
	}
	public function DeleteTemplateFromFunnel(Request $request){
		$inputs = $request->input();
		
		$funnel_step_id = $inputs['funnel_step_id'];
		$funnelData=PageDetail::where('id', $funnel_step_id)->update(['is_deleted' =>1]);
		if($funnelData==1)
		{
			$status = 'success';
			$finalArr = array('message'=>"Template deleted successfully");
		}
		else
		{
			$status = 'fail';
			$finalArr = array('message'=>"An error occur.Please try again !");
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function sortFunnelStep(Request $request)
	{
		$inputs = $request->input();
		$sortedList = $inputs['sorted_list'];
		$sortedarray=explode(' ',$sortedList);
		for($i=0;$i<count($sortedarray);$i++)
		{
			$step_id=$sortedarray[$i];
			$sor_order=$i+1;
			PageDetail::where('id', $step_id)->update(['sort_order' =>$sor_order]);
			
		}
		$status = 'success';
		$finalArr = array('TempArr'=>"Update funnel step");
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function getFunnelAllTemplates(Request $request)
	{
		$inputs = $request->input();
    	$Myfunnel_id = $inputs['funnel_id'];
    	
		$templatepageData = PageDetail::WhereRaw('Myfunnel_id = "'.$Myfunnel_id.'" and is_deleted= 0')
		->select('id','name','template_id', 'sort_order','slug','is_default')->orderBy('sort_order', 'ASC')->get();
		$templatedata=$templatepageData->toArray();
		//print_r($templatedata); die;
		if(count($templatedata >0))
		{
			$funnelsdata = Myfunnel::find($Myfunnel_id);
			$i=0;
			foreach($templatedata as $steps)
			{
				$stepId=$steps['id'];
				$tempcode='';
				if(isset($steps['template_id']) && $steps['template_id'] !='0')
				{
					$template_id=$steps['template_id'];
					$templateDAta=Template::find($template_id);
					$tempcode=$templateDAta->temp_code;
					$templateThumbImage=PageDetail::find($stepId);
					if($templateThumbImage->thumbnail_image){
						$tempimage = $templateThumbImage->thumbnail_image;
					}
					else{
						$tempimage=$templateDAta->image;
					}					
					$tempName=$templateDAta->slug;
				}
				
				$contacts=Ebookuser::getAllRegisterUserOfTemplate($stepId);
				$visitors=Uniquehit::getAllVisitorOfTemplate($stepId);
				$templatedata[$i]['contacts']=count($contacts);
				$templatedata[$i]['visitors']=count($visitors);
				$templatedata[$i]['temp_code']=$tempcode;
				$templatedata[$i]['image']=$tempimage;
				$templatedata[$i]['temp_slug']=$tempName;
				$i++;
			}
			$status = 'success';
			$finalArr = array('TempArr'=>$templatedata,'funnel_name'=>$funnelsdata->funnel_name);
		}
		else 
		{
			$status = 'fail';
			$finalArr = array('TempArr'=>'');
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		 
	}
	
	//*****************************Get Funnels Contacts********************************//
	public function getFunnelAllContacts(Request $request)
		{   
			$inputs = $request->input();
	    	$Myfunnel_id = $inputs['funnel_id'];
	    	
	    	$templatepageData = PageDetail::WhereRaw('Myfunnel_id = "'.$Myfunnel_id.'" and is_deleted= 0')
			->select('id','name')->orderBy('sort_order', 'ASC')->get();
			$templatedata=$templatepageData->toArray();
			if(count($templatedata >0))
			{
				$i=0;
				$allContacts=array();
				$alldata=array();

				foreach($templatedata as $steps)
				{
					$alldata=array();
					$stepId=$steps['id'];
					$startDate='';
					$endDate='';
					if($inputs['start_date'] !='' && $inputs['end_date'] !=''){
						$startDate = $inputs['start_date'];
						$endDate = $inputs['end_date'];
						$result=Ebookuser::getAllcontactsOfFunnel($stepId,$startDate,$endDate);
					}
					else
					{
						$result=Ebookuser::getAllcontactsOfFunnel($stepId,$startDate,$endDate);
						//$result=PageDetail::find($stepId)->Ebookuser;
					}
					//print_r($result);
					$res=$result->toArray();
				 if(!empty($res))
			     {
				      $j=0;
				      foreach($res as $contactsdetail)
				      {
				       if($res[$j]['name']=='')
				       {
				        $res[$j]['name']='N/A';
				       }
				       if($res[$j]['contact_no']=='')
				       {
				        $res[$j]['contact_no']='N/A';
				       }
				       $alldata=array("Name"=>$res[$j]['name'],'Email'=>$res[$j]['email'],'ContactNumber'=>$res[$j]['contact_no'],'StepName'=>$steps['name'],'Date'=>$res[$j]['created_at']);
				       array_push($allContacts,$alldata);
				       $j++;
				      }
			     }
					$i++;
				}//die;

				$status = 'success';
				$finalArr = array('contactArr'=>$allContacts);
			}
			else 
			{
				$status = 'fail';
				$finalArr = array('contactArr'=>'');
			}
			
			$result = Common::sendRequest($status,$finalArr);
			return $result;
			 
		}
		
	
	//*************************Get Category data from database*******************************//
	public function getCategoryDetail(Request $request)
	{
		$inputs = $request->input();
		$categoryId = $inputs['category_id'];
		
		if($categoryId == 0)
	    {
	    	$status='1';
	    	$categoryData = Template::whereRaw('status = "'.$status.'"') ->orderBy('sort_order', 'asc')->get();
	    }
	    else
	    {
			$categoryData = Template::whereRaw('category_id = "'.$categoryId.'"')  ->orderBy('sort_order', 'asc')->get();
	    }
		$categoryData =  $categoryData->toArray();
		foreach($categoryData as $detail)
		{
			$detailArr['name'] = $detail['name'];   
			$detailArr['id'] = $detail['id'];              // template name
			$detailArr['image'] = $detail['image'];
			$detailArr['slug'] = $detail['slug'];
			$detailArr['temp_code'] = $detail['temp_code'];
			$finalcatData[] = $detailArr;
		} 
		$status = 'success';
		
		$finalArr = array('catArr'=>$finalcatData);
		$result = Common::sendRequest($status,$finalArr);
		return $result; 

	}
	
	
	//***********************Get Autoresponder data*************************//
	public function getApiListDetail(Request $request)
	{
		$inputs = $request->input();
		$token = $inputs['Token'];
		$userId =User::getId($token);
		/*$tempslug = $inputs['temp_slug'];
		$tempId  = Template::whereRaw('temp_code = "'.$tempslug.'"')->get();
		$templateId =  $tempId->toArray();
		$template_id = $templateId[0]['id'];*/
		$autoResponderData = Autoresponder::whereRaw('is_active= "1" and user_id="'.$userId.'"')->get();
		$autoResponder =  $autoResponderData->toArray();
		$autoResponderArr=array();
		foreach($autoResponder as $response)
		{
			$responseArr['name'] = $response['name'];
			$responseArr['id'] = $response['id'];
			$responseArr['api_list'] = json_decode($response['api_list']);
			$autoResponderArr[] = $responseArr;
		}
		
		$status = 'success';
			
		$finalArr1 = array('autoResponder'=>$autoResponderArr);
		$result = Common::sendRequest($status,$finalArr1);
		return $result;
		
	}

	
	//***********************Get Autoresponder list on refresh data*************************//
	public function getReloadListDetail(Request $request)
	{
		$inputs = $request->input();
			$name = $inputs['api_id'];
		$autoResponderData = Autoresponder::whereRaw('is_active= "1"')->get();
		$autoResponder =  $autoResponderData->toArray();
		foreach($autoResponder as $response)
		{
			$responseArr['name'] = $response['name'];
			$responseArr['id'] = $response['id'];
			$responseArr['api_list'] = json_decode($response['api_list']);
			$autoResponderArr[] = $responseArr;
		}
		
		$status = 'success';
			
		$finalArr1 = array('autoResponderRefresh'=>$autoResponderArr);
		$result = Common::sendRequest($status,$finalArr1);
		return $result;
	
	}

	//*********************** Get All funeels of user ************************//
	public function getAllFunnels(Request $request)
	{
		$inputs = $request->input();
		$token = $inputs['token'];
		$userId =User::getId($token);
		$funnelData=array();
		$MyfunnelData = Myfunnel::whereRaw('user_id = "'.$userId.'" and is_deleted="0"')->select('id','user_id', 'funnel_name as name','cname', 'slug', 'group_tag','created_at','updated_at')->orderBy('updated_at', 'ASC')->get();
		if(isset($MyfunnelData))
		{
			$funnelData =  $MyfunnelData->toArray();
			//echo "<pre>";print_r($funnelData);die;
			$i=0;
			foreach($funnelData as $myfunnel)
			{
				$funnel_id=$myfunnel['id'];
				
				$funnelsSteps = Myfunnel::find($funnel_id)->pagedetail;
				
				$stepdata=$funnelsSteps->toArray();
				
				$funnelData[$i]['funnelSteps']=$stepdata;
				
				$i++;
			}
			
			$status = 'success';
			$finalArr = array('funnelArr'=>$funnelData);
			
		}
		else 
		{
			$status = 'Fail';
			$finalArr = array('funnelArr'=>"No data found for this user");
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	function deleteMyCompletefunnel(Request $request)
	{
		$inputs = $request->input();
		$funnelId = $inputs['funnel_id'];
		$funnelData=Myfunnel::where('id', $funnelId)->update(['is_deleted' =>1]);
		if($funnelData==1)
		{
			$status = 'success';
			$finalArr = array('message'=>"Funnel deleted successfully");
		}
		else 
		{
			$status = 'fail';
			$finalArr = array('message'=>"An error occur.Please try again !");
		}
	
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function AddTemplateInFunnelStep(Request $request)
	{ 
		$inputs = $request->input();
		$templateID = $inputs['template_id'];
		$stepId = $inputs['funnel_step_id'];
		$result=PageDetail::where('id', $stepId)->update(['template_id' =>$templateID]);
		
		if($result)
		{
			$resultPAgeDetail=PageDetail::find($stepId);
			$slug=$resultPAgeDetail->slug;
			$MyfunnelData = PageSection::whereRaw('template_id = "'.$templateID.'" and page_detail_id="0"')->orderBy('id', 'asc')->get();
			$funnelData =  $MyfunnelData->toArray();
			
			for($i=0;$i<count($funnelData);$i++)
			{
				$pagesectiondatanew= new PageSection;
				$pagesectiondatanew->template_id=$funnelData[$i]['template_id'];
				$pagesectiondatanew->option_id=$funnelData[$i]['option_id'];
				$pagesectiondatanew->page_detail_id=$stepId;
				$pagesectiondatanew->value=$funnelData[$i]['value'];
				$pagesectiondatanew->status='1';
				$pagesectiondatanew->created_at=date('Y-m-d H:i:s');
				$pagesectiondatanew->save();
				
			}
			$status = 'success';
			$finalArr = array('message'=>"Template added successfully",'slug'=>$slug);
				
		}
		else 
		{
			$status="Fail";
			$finalArr = array('message'=>"Inputs not found Please try again.");
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		
	}
	public function createThumbnailImages(Request $request)
	{
		$inputs = $request->input();
		$image = $inputs['image'];
		$pageDetailId = $inputs['pageDetailId'];
		$template_id = $inputs['templateId']; 
		//$filedir = $inputs['filedir'];
		
		$nam = time();
		$ran = rand(10, 999);
		$imgname = $nam.$ran;
		$name = $imgname.".png";
		$filedir = 'public/assets/admin/images/thumbnail-images/';
		$decoded = base64_decode(str_replace('data:image/png;base64,', '', $image));
	    //$image = str_replace('data:image/png;base64,', '', $image);
		//$image = str_replace(' ', '+', $image);
		//$decoded = base64_decode($image);
		$filepath = $filedir.$name;
		$res = file_put_contents($filepath, $decoded);
		
		if($pageDetailId !=''){
			$PageDetailData = PageDetail::find($pageDetailId);
			$affectedRows = PageDetail::where('id','=', $PageDetailData->id)
			->where('template_id', '=', $template_id)->update(['thumbnail_image' => $name]);
		}
		
		if($res)
		{
			$status = 'success';
			$finalArr = array('message'=>"Image uploaded successfully");
		}
		else
		{
			$status="Fail";
			$finalArr = array('message'=>"Image does not uploaded");
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		
	}
	public function funnelDetails(Request $request)
	{
		$data=$request->input();
		$token = $data['token'];
		$funnel_id=$data['funnel_id'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$funnelDeail=Myfunnel::find($funnel_id);
			if($funnelDeail['user_id']==$user_id)
			{
				$funnelDetail=$funnelDeail->toArray();
				$status="success";
				$finalArr=array("funneldetail"=>$funnelDetail);
			}
			else 
			{
				$status="Fail";
				$finalArr=array("message"=>"You have no permission to access this funnel details.");
			}
			//echo $funnelDeail['user_id'];print_r($funnelDeail['id']);die;
		}
		else
		{
			$status="Fail";
			$finalArr=array("message"=>"User not exist");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function updatefunnelDetails(Request $request)
	{
		$data=$request->input();
		$token = $data['token'];
		$funnel_id=$data['funnel_id'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$funnelDeail=Myfunnel::find($funnel_id);
			if($funnelDeail['user_id']==$user_id)
			{
				$funnelDeail->funnel_name=$data['funnel_name'];
				$funnelDeail->cname=$data['funnel_cname'];
				if($funnelDeail->save())
				{
					$id=$funnelDeail->id;
					$funnelDetail=Myfunnel::find($id);
					$status="success";
					$finalArr=array("funneldetail"=>$funnelDetail,"message"=>"Your funnel detail has been updated");
				}
				
			}
			else
			{
				$status="Fail";
				$finalArr=array("message"=>"You have no permission to access this funnel details.");
			}
			//echo $funnelDeail['user_id'];print_r($funnelDeail['id']);die;
		}
		else
		{
			$status="Fail";
			$finalArr=array("message"=>"User not exist");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function updatefunnelStepPath(Request $request)
	{
		$inputs = $request->input();
		$token = $inputs['token'];
		$user_id =User::getId($token);
		$step_id = $inputs['step_id'];
		$lstep_path = $inputs['funnelStepPath'];
		if($user_id)
		{
			$pageDetail=PageDetail::find($step_id);
			$pageDetail->slug=$lstep_path;
			if($pageDetail->save())
			{
				$status="success";
				$finalArr=array("message"=>"Path has been updated");
			}
		
		}
		else
		{
			$status="Fail";
			$finalArr=array("message"=>"User not exist");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		}
	public function checkYourStepPath(Request $request)
	{
		$data = $request->input();
		$step_path = $data['step_path'];
		$token = $data['token'];
		$user_id =User::getId($token);
		//$res = User::find($user_id);
		$stepData=PageDetail::WhereRaw('id != "'.$user_id.'" and slug="'.$step_path.'"')->get();
		$allstep =  $stepData->toArray();
		if(count($allstep) >0)
		{
			$status="success";
			$finalArr=array("message"=>"Path is already exist.");
		}
		else
		{
			$status="successwithnoerro";
			$finalArr=array("message"=>"");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function getAllTemplatePath()
	{
		//$token = $data['token'];
		//$user_id =User::getId($token);
		//$res = User::find($user_id);
		$stepData=PageDetail::all();
		$allsteps =  $stepData->toArray();
		if(count($allsteps) >0)
		{
			$allstep=array();
			for($i=0;$i<count($allsteps);$i++)
			{
				array_push($allstep,$allsteps[$i]['slug']);
			}
			$status="success";
			$finalArr=array('template_path'=>$allstep);
		}
		else
		{
			$status="successwithnoerro";
			$finalArr=array("message"=>"");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
}
