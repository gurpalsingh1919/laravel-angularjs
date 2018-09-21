<?php namespace App\helpers;
use AWeberAPI;
use App\Autoresponder\GetResponse;
use App\Autoresponder\IContactApi;

class AutoResponderData 
{
	private $autoResponderObj; //main object of autoResponder class, its value depend on the calling api
	private $aweberAccountObj = ''; //for aweber
	public static function getAutoresponderData($api, $data)
	{
		
		switch (strtolower($api)) 
		{  
			case 'aweber':
				$consumerKey=$data['consumerKey'];
				$consumerSecret=$data['consumerSecret'];
				$aweber = new AWeberAPI($consumerKey, $consumerSecret);
				if (!key_exists('access_token', $data))
				{
					if (!key_exists('oauth_token', $data))
					{
							//$callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].'/pagebuilder_git/CODEBASE/#/mysettings';
						

						$callbackUrl = 'https://'.$_SERVER['HTTP_HOST'].'/mysettings/autoresponder';

						list($requestToken, $requestTokenSecret) =$aweber->getRequestToken($callbackUrl);
						return array('request_token_secret' => $requestTokenSecret, 'authorize_url' => $aweber->getAuthorizeUrl());
					}
					$aweber->user->tokenSecret = $data['request_token_secret'];
					$aweber->user->requestToken = $data['oauth_token'];
					if (key_exists('oauth_verifier', $data)) 
					{
						$aweber->user->verifier = $data['oauth_verifier'];
					}
					list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
					$account = $aweber->getAccount($accessToken, $accessTokenSecret);
					return array('access_token'=>$accessToken,'access_token_secret'=>$accessTokenSecret,'account'=>$account);
				
				} 
				else if (isset($data['access_token']) && isset($data['access_token_secret'])) 
				{
					$account = $aweber->getAccount($data['access_token'], $data['access_token_secret']);
				}
				break;
			case 'getresponse':
				//require_once(getresponse_lib);
				$getresponse = new GetResponse($data['apiKey'], FALSE);
				
				$data= $getresponse->getCampaigns();
				$data = (array) $data;
				$listKeys = array_keys($data);
				
				if (!empty($listKeys)) {
					$responseData=array();
					foreach ($listKeys as $key) {
						//$prepData['lists'][] = array('id' => $key, 'name' => $data[$key]->name);
						$responseData[]= array('id' => $key,'name'=>$data[$key]->name);
						
					}
					$prepData= json_encode($responseData);
					return $prepData;
				}
				
				break;
				case 'icontact':
                  iContactApi::getInstance()->setConfig(array(
                        'appId' => $data['api_id'],
                        'apiPassword' => $data['api_pwd'],
                        'apiUsername' => $data['api_user_name']
                    ),FALSE);
                    $response = iContactApi::getInstance();
                    $data=$response->getLists(); //call to authenticate credentials
                  	if (!empty($data)) {
                    	foreach ($data as $obj) {
                    		//$prepData['lists'][] = array('id' => $obj->listId, 'name' => $obj->name);
                    		$responseData[]= array('id' => $obj->listId,'name'=>$obj->name);
                    	}
                    	$prepData= json_encode($responseData);
                    }
                    else 
                    {
                    	$prepData='';
                    }
                   
                    return $prepData;
                    break;
				
		}
	}
	
	/**
	 * function to authenticate oauth2 basically, but currently it is jus calling getObject function
	 * @var string $api a string with name of the called api
	 * @var array $data an array with data of the called api
	 * @return Returns urls for some apis for oauth authentication redirection
	 */
	
	public static function authenticate($api, $data) {
		try {
			return AutoResponderData::getObject($api, $data);
		} catch (\Exception $e) {
			throw new \Exception('Invalid Credentials', '777');
		}
	}
	
	
	/**
	 * function to add contact to lists for called api
	 * @var string $api a string with name of the called api
	 * @var array $data an array with data of the called api
	 * @return Returns added contact data from called api
	 */
	public static function addContact($api, $data,$appData) {
		try {
			//$formattedData = $this->prepContact($api, $data);
			$formattedData=$data;
			$response = array();
			switch (strtolower($api)) {
				
				case 'getresponse':
					
					$getresponses = new GetResponse($appData['apiKey'], FALSE);
				//	echo "<pre>";print_r($getresponses);die;
					//check to see if a contact with the email address already exists in the account
					$getResponse = $getresponses->getContactsByEmail($formattedData['email_id'], $formattedData['list_id']);
					
					//if (!$getResponse->rUrian) {
						//creating contact if it does not exist in list
						$getResponse = $getresponses->addContact($formattedData['list_id'], $formattedData['first_name'] . ' ' . $formattedData['phone_no'], $formattedData['email_id'], 'insert');
						
						if ($getResponse && $getResponse->queued) {
							$response = 'success';
						}
					//}
					break;
				case 'icontact':
					iContactApi::getInstance()->setConfig(array(
						  'appId' => $appData['app_id'],
                        'apiPassword' => $appData['consumer_secret'],
                        'apiUsername' => $appData['consumer_key']
						),FALSE);
						
                    $autoResponderObj =iContactApi::getInstance();
                	$getResponse = $autoResponderObj->getContactWithEmailList($formattedData['email_id'], $formattedData['list_id']);
					if (empty($getResponse)) {
						//create contact
						$getResponse = $autoResponderObj->addContact($formattedData['email_id'], null, null, $formattedData['first_name']);
						if ($getResponse) {
							//subscribe to list
							$getResponse = $autoResponderObj->subscribeContactToList($getResponse->contactId, $formattedData['list_id'], 'normal');
							if ($getResponse) {
								$response = 'success';
							}
						}
					}
					break;
				
				case 'aweber' :
					//echo $formattedData['email_id'];die;
					$consumerKey=$appData['consumer_key'];
					$consumerSecret=$appData['consumer_secret'];
					$aweber = new AWeberAPI($consumerKey, $consumerSecret);
					$aweberAccountObj = $aweber->getAccount($appData['access_token'], $appData['access_token_secret']);
					
	
					$account_id = $aweberAccountObj->id;
					$list_id = $data['list_id'];
					$listURL = "/accounts/{$account_id}/lists/{$list_id}";
					$list = $aweberAccountObj->loadFromUrl($listURL);
					# create a subscriber
					$subscribers = $list->subscribers;
	
					//code to search for subscriber if found then update it otherwise add it
					$searchParam = array('email' => strtolower($formattedData['email_id']));
				
					$found_subscribers = $subscribers->find($searchParam);
					
					if ($found_subscribers) {
						if (empty($found_subscribers->data['entries'])) {
					 		//create new subscriber
							$formattedData['email']=$formattedData['email_id'];
							$getResponse = $subscribers->create($formattedData);
							if ($getResponse && $getResponse->data['id']) {
								$response = 'success';
							}
						} else if (!empty($found_subscribers->data['entries']) && key_exists('status', $found_subscribers->data['entries'][0]) && strtolower($found_subscribers->data['entries'][0]['status']) == 'unsubscribed') {
	
							foreach ($found_subscribers as $subscriber) {
								$subscriber->status = 'subscribed';
								$getResponse = $subscriber->save();
								if ($getResponse) {
									$response = 'success';
								}
							}
						}
					}
					break;
				case 'infusionsoft' :
					try {
						$contactId = $this->autoResponderObj->contacts->addWithDupCheck($formattedData, 'Email');
						if ($contactId) {
							$getResponse = $this->autoResponderObj->emails->optIn($formattedData['Email'], 'EasyWebinar');
							if (key_exists('tag_id', $data) && trim($data['tag_id']) != '') {
								$getResponse = $this->autoResponderObj->contacts->addToGroup((int) $contactId, (int) trim($data['tag_id']));
								if ($getResponse) {
									$response = 'success';
								}
							}
							if (key_exists('list_id', $data) && trim($data['list_id']) != '') {
								$getResponse = $this->autoResponderObj->contacts->addToCampaign((int) $contactId, (int) trim($data['list_id']));
								if ($getResponse) {
									$response = 'success';
								}
							}
						}
					} catch (\Infusionsoft\TokenExpiredException $e) {
						$tokenObj = $this->autoResponderObj->refreshAccessToken();
						if ($tokenObj->refreshToken) {
							\app\models\WebinarUserApis::setInfusionSoftTokens($data['id'], array('access_token' => serialize($tokenObj), 'refresh_token' => $tokenObj->refreshToken));
						}
						$contactId = $this->autoResponderObj->contacts->addWithDupCheck($formattedData, 'Email');
						if ($contactId) {
							$getResponse = $this->autoResponderObj->emails->optIn($formattedData['Email'], 'EasyWebinar');
							if (key_exists('tag_id', $data) && trim($data['tag_id']) != '') {
								$getResponse = $this->autoResponderObj->contacts->addToGroup((int) $contactId, (int) trim($data['tag_id']));
								if ($getResponse) {
									$response = 'success';
								}
							}
							if (key_exists('list_id', $data) && trim($data['list_id']) != '') {
								$getResponse = $this->autoResponderObj->contacts->addToCampaign((int) $contactId, (int) trim($data['list_id']));
								if ($getResponse) {
									$response = 'success';
								}
							}
						}
					}
					break;
			}
		}
		catch (\Exception $e)
		{
			throw new \Exception($e->getMessage());
		}
		return $response;
	}
	
	/**
	 * function to prepare contact data for called api
	 * @var string $api a string with name of the called api
	 * @var array $data an array with data of the called api
	 * @return Returns formatted contact data according to called api
	 */
	public function prepContact($api, $data) {
		$formattedData = array();
		switch (strtolower($api)) {
			
			case 'getresponse':
				$formattedData = $data;
				break;
			case 'icontact':
				$formattedData = $data;
				break;
			
			case 'aweber':
				$name = (key_exists('first_name', $data)) ? trim($data['first_name']) : '';
				if (strlen(trim($name)) > 0) {
					if (key_exists('last_name', $data) && trim($data['last_name']) != '') {
						$name .= ' '.$data['last_name'];
					}
				} else {
					$name = (key_exists('last_name', $data)) ? trim($data['last_name']) : '';
				}
				$formattedData = array(
						'email' => trim($data['email_id']),
						'name' => $name
				);
				break;
			case 'infusionsoft' :
				$formattedData = array(
				'FirstName' => (key_exists('first_name', $data)) ? trim($data['first_name']) : '',
				'LastName' => (key_exists('last_name', $data)) ? trim($data['last_name']) : '',
				'Email' => trim($data['email_id'])
				);
				break;
		}
		return $formattedData;
	}
	

	public function getObject($api, $data) {
		try {
			switch (strtolower($api)) {
				
				case 'getresponse':
					require_once(getresponse_lib);
					$this->autoResponderObj = new \GetResponse($data['api_key'], FALSE);
					if ($this->autoResponderObj->ping() != 'pong') {
						throw new \Exception('Invalid Credentials');
					}
					break;
				case 'icontact':
					require_once(icontact_lib);
					\iContactApi::getInstance()->setConfig(array(
							'appId' => $data['api_id'],
							'apiPassword' => $data['api_pwd'],
							'apiUsername' => $data['api_user_name']
					));
					$this->autoResponderObj = \iContactApi::getInstance();
					$this->autoResponderObj->getLists(); //call to authenticate credentials
					break;
			
				case 'aweber':
					require_once(aweber_lib);
					$this->autoResponderObj = new \AWeberAPI($data['consumer_key'], $data['consumer_secret']);
					if (!key_exists('access_token', $data)) {
						if (empty($arrGet['oauth_token']) && !key_exists('oauth_token', $data)) {
							if ($_SERVER['HTTP_HOST'] === 'localhost') {
								$callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/ewp-saas/application/app/#/account/autoresponder';
							} else {
								$callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/app/#/account/autoresponder';
							}
	
							list($requestToken, $requestTokenSecret) = $this->autoResponderObj->getRequestToken($callbackUrl);
							return array('request_token_secret' => $requestTokenSecret, 'authorize_url' => $this->autoResponderObj->getAuthorizeUrl());
						}
						$this->autoResponderObj->user->tokenSecret = $data['request_token_secret'];
						$this->autoResponderObj->user->requestToken = $data['oauth_token'];
						if (key_exists('oauth_verifier', $data)) {
							$this->autoResponderObj->user->verifier = $data['oauth_verifier'];
						}
	
						try {
							list($accessToken, $accessTokenSecret) = $this->autoResponderObj->getAccessToken();
							return array('access_token' => $accessToken, 'access_token_secret' => $accessTokenSecret);
						} catch (\Exception $e) {
							throw new \Exception('Error');
						}
					} else if (isset($data['access_token']) && isset($data['access_token_secret'])) {
						$this->aweberAccountObj = $this->autoResponderObj->getAccount($data['access_token'], $data['access_token_secret']);
					}
					break;
				case 'infusionsoft' :
					require_once(infusionsoft_lib);
					if ($_SERVER['HTTP_HOST'] === 'localhost') {
						$callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/ewp-saas/application/app/#/account/autoresponder';
					} else {
						$callbackUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/app/#/account/autoresponder';
					}
					$this->autoResponderObj = new \Infusionsoft\Infusionsoft(array(
							'clientId' => $data['consumer_key'],
							'clientSecret' => $data['consumer_secret'],
							'redirectUri' => $callbackUrl,
					));
	
					if (key_exists('oauth_verifier', $data) and ! key_exists('access_token', $data)) {
	
						$infusionsoftObj = $this->autoResponderObj->requestAccessToken($data['oauth_verifier']);
						if ($infusionsoftObj) {
							return array('access_token' => serialize($infusionsoftObj), 'refresh_token' => $infusionsoftObj->refreshToken);
						} else {
							return array();
						}
					}
	
					if (key_exists('access_token', $data)) {
						$this->autoResponderObj->setToken(unserialize($data['access_token']));
					}
	
					if (!$this->autoResponderObj->getToken()) {
						return array('authorize_url' => $this->autoResponderObj->getAuthorizationUrl());
					}
					break;
			}
		} catch (\Exception $e) {
			throw new \Exception('Invalid Credentials', '777');
		}
	}
	
	
	
	/**
	 * function to get lists for called api
	 * @var string $api a string with name of the called api
	 * @var array $data an array with data of the called api,but it is empty in most cases for the time being
	 * @return Returns list data from called api
	 */

}
