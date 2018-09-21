<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('main');
});



	Route::group(['prefix' => 'myfunnel'], function()
	{
		/*Route::get('/getTemplateDetail', array(
		'as'   => 'Myfunnel.getTemplateDetail',
		'uses' => 'MyfunnelController@getTemplateDetail')
		);*/
		Route::post('getCreatedFunnel', 'MyfunnelController@getTemplateDetail');
		Route::post('SubscribeNewUser', 'MyfunnelController@SubscribeNewUser');
		
		//Route::get('MyfunnelController@getTemplateDetail');
	});
	Route::group(['prefix' => 'payment'], function()
	{
		Route::post('Paymentsuccess', 'PaymentController@Paymentsuccess');
		Route::post('getpaymentplans', 'PaymentController@getpaymentplans');
		Route::post('userRegistration', 'PaymentController@userRegistration');
		Route::post('repayment', 'PaymentController@getrepayment');
		Route::post('ipnResponse', 'PaymentController@ipnResponse');
		Route::post('addNewPaymentPlan', 'PaymentController@addNewPaymentPlan');
		Route::post('updatePaymentPlan', 'PaymentController@updatePaymentPlan');
		Route::post('getPlanDetail', 'PaymentController@getPlanDetail');
		Route::post('deletePaymentPlan', 'PaymentController@deletePaymentPlan');
		Route::post('getUserAllPlan', 'PaymentController@getUserAllPlan');
		Route::post('paymentDeclineRequest', 'PaymentController@paymentDeclineRequest');
		Route::post('cancelThePaymentProgrammatically', 'PaymentController@cancelThePaymentProgrammatically');
		Route::post('getAllRefundList', 'PaymentController@getAllRefundList');
		Route::post('getAllCancellationList', 'PaymentController@getAllCancellationList');
		Route::post('changeUserPaymentPlan', 'PaymentController@changeUserPaymentPlan');
		Route::post('getDetailAndCreateRecurring', 'PaymentController@getDetailAndCreateRecurring');
		
	});

Route::group(['prefix' => 'api'], function()
{ 
	Route::resource('alluser', 'AuthenticateController@index');
	Route::post('authenticate', 'AuthenticateController@authenticate');
	Route::post('aweberRequest', 'AuthenticateController@aweberRequest');
	Route::post('getAccessToken', 'AuthenticateController@getAccessToken');
	Route::post('getApiUserList', 'AuthenticateController@getApiUserList');
	Route::post('getResponseRequest', 'AuthenticateController@getResponseRequest');
	Route::post('getIcontactRequest', 'AuthenticateController@getIcontactRequest');
	Route::post('changeAutoresponderStatus', 'AuthenticateController@changeAutoresponderStatus');
	Route::post('getUserStatus', 'AuthenticateController@getUserStatus');
	
	Route::post('getUserDetail', 'AuthenticateController@getUserDetail');
	Route::post('updatePassword', 'AuthenticateController@updatePassword');
	Route::post('checkYourDomain', 'AuthenticateController@checkYourDomain');
	Route::post('checkYourEmail', 'AuthenticateController@checkYourEmail');
	Route::post('updateProfileInfo', 'AuthenticateController@updateProfileInfo');
	Route::post('checkForgotEmail', 'AuthenticateController@checkForgotEmail');
	Route::post('resetPassword', 'AuthenticateController@resetPassword');
	Route::post('updateUserProfileInfo', 'AuthenticateController@updateUserProfileInfo');
	
});
Route::group(['prefix' => 'admin'], function()
{
	Route::post('template', 'TemplateController@editTemplateDetail');
	Route::post('uploadImage', 'TemplateController@imageUpload');
	Route::post('deleteimage', 'TemplateController@deleteImage');
	Route::post('ebookimageupload', 'TemplateController@UploadEbookImage');
	Route::post('save', 'TemplateController@updateTemplateDetail');
	Route::post('addNewFunnel', 'TemplateController@addNewFunnel');
	Route::post('categoryData', 'TemplateController@getCategoryDetail');
	Route::post('calltopopup', 'TemplateController@getCustomPopupDetail');
	Route::post('apilist', 'TemplateController@getApiListDetail');	
	Route::post('reloadapilist', 'TemplateController@getReloadListDetail');
	Route::post('ebookdeleteimage', 'TemplateController@ebookdeleteimage');	
	Route::post('funnelAlltemplates', 'TemplateController@getFunnelAllTemplates');
	Route::post('addfunnelstep', 'TemplateController@addFunnelStep');
	Route::post('sortFunnelStep', 'TemplateController@sortFunnelStep');
	Route::post('allFunnelsOfUser', 'TemplateController@getAllFunnels');
	Route::post('myfunnelWithsteps', 'TemplateController@getmyfunnelWithsteps');
	Route::post('deletdMyCompletefunnel', 'TemplateController@deleteMyCompletefunnel');
	Route::post('AddTemplateInFunnelStep', 'TemplateController@AddTemplateInFunnelStep');
	Route::post('backgroundimageupload', 'TemplateController@backgroundimageupload');
	Route::post('DeleteTemplateFromFunnel', 'TemplateController@DeleteTemplateFromFunnel');
	Route::post('funnelAllContacts', 'TemplateController@getFunnelAllContacts');
	Route::post('createThumbnailImages', 'TemplateController@createThumbnailImages');
	Route::post('funnelDetails', 'TemplateController@funnelDetails');
	Route::post('updatefunnelDetails', 'TemplateController@updatefunnelDetails');
	Route::post('updatefunnelStepPath', 'TemplateController@updatefunnelStepPath');
	Route::post('checkYourStepPath', 'TemplateController@checkYourStepPath');
	Route::post('getAllTemplatePath', 'TemplateController@getAllTemplatePath');
});
Route::group(['prefix' => 'superadmin'], function()
{ 
	Route::post('authenticateAdmin', 'AdminController@authenticateAdmin');
	Route::post('userRegister', 'AdminController@userRegister');
	Route::post('sendResetPasswordEmail', 'AdminController@sendResetEmail');
	Route::post('getUserPaymentHistory', 'AdminController@getUserPaymentHistory');
});

Route::any('{path?}', function()
{

	return view('main');
	
})->where("path", ".+");

