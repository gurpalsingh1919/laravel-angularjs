//(function() {
'use strict';
var app=angular.module('authApp', [ 'oc.lazyLoad',
                                    'color.picker',
                                    'ngWYSIWYG',
                                    'ui.router',
                                    'satellizer',
                                    'colorpicker.module',
                                    'angularFileUpload',
                                    'ngCkeditor',
                                    'toggle-switch',
                                    'ngSanitize',
                                    'ui.bootstrap',
                                    'angularUtils.directives.dirPagination','ui.sortable'
                                    ])
		.config(function($stateProvider,$httpProvider, $urlRouterProvider, $authProvider,$ocLazyLoadProvider,$locationProvider) {
  $httpProvider.interceptors.push('MyAppAuthIn');
		  $ocLazyLoadProvider.config({
			debug:false,
			events:true,

			});
		
			   $locationProvider.html5Mode(true);
			   $authProvider.loginUrl = 'api/authenticate';


			$stateProvider
			.state('home', {
				url: '/',
				templateUrl: 'public/views/frontend/home.html',
				controller: 'AuthController as auth',
				 resolve: {
					loadMyDirectives:function($ocLazyLoad){
						return $ocLazyLoad.load(
						{
							name:'authApp',
							files:[
							 'public/assets/admin/js/loading.js',
							 'public/scripts/directives/headers/home-header.js',
							 'public/assets/frontend/css/custom_frontend.css',
							]
						})
					 }
				}
			}).state('userauth', {
					url: '/login',
					templateUrl: 'public/views/frontend/login.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/home-header.js',
								 'public/assets/frontend/css/custom_frontend.css',
								]
							})
						 }
					}
				}).state('adminlogin', {
					url: '/adminlogin',
					templateUrl: 'public/views/frontend/admin-login.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								  'public/scripts/adminController.js',
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/home-header.js',
								 'public/assets/frontend/css/custom_frontend.css',
								]
							})
						 }
					}
				}).state('paypal', {   											
				    url: '/paypal',
				    templateUrl: 'public/views/frontend/paypal-form.html',
				    controller: 'AuthController as auth',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/headers/home-header.js',
								       'public/assets/frontend/css/custom_frontend.css',
									'public/assets/frontend/css/paypal.css',
									]
							})
						 }
					}
					
				   }).state('paymentstatus', {   											
					    url: '/paymentstatus',
					    templateUrl: 'public/views/admin/payment-status.html',
					    controller: 'AuthController as auth',
					    resolve: {
							loadMyDirectives:function($ocLazyLoad){
								return $ocLazyLoad.load(
								{
									name:'authApp',
									files:[
									       'public/assets/admin/js/loading.js',
											 'public/scripts/directives/headers/login-after.js',
											 'public/scripts/directives/popup/admin-popup.js',
											 'public/assets/admin/css/custom_admin.css',
										
										]
								})
							 }
						}
						
					   }).state('forgotpassword', {
					url: '/forgotpassword',
					templateUrl: 'public/views/frontend/forgotpassword.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/home-header.js',
								 'public/assets/frontend/css/custom_frontend.css',
								]
							})
						 }
					}
				}).state('resetpassword', {
					url: '/resetpassword/{id}',
					templateUrl: 'public/views/frontend/resetpassword.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/home-header.js',
								 'public/assets/frontend/css/custom_frontend.css',
								]
							})
						 }
					}
				})
				.state('usersignup', {
					url: '/usersignup',
					templateUrl: 'public/views/frontend/signup.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/home-header.js',
								 'public/assets/frontend/css/custom_frontend.css',
								]
							})
						 }
					}
				})
				.state('dashboard', {
					url: '/dashboard',
					templateUrl: 'public/views/admin/select-funnel.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/login-after.js',
								 'public/scripts/directives/popup/admin-popup.js',
								 'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				})
				/*.state('admindashboard', {
					url: '/admindashboard/{id}',
					templateUrl: 'public/views/admin/admindashboard.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/scripts/adminController.js',
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/super-admin-header.js',
								 'public/scripts/directives/popup/admin-popup.js',
								 'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				})
			 .state('admindashboard-user', {
				 url: '/admindashboard/user/{id}',
			templateUrl: 'public/views/admin/admindashboard.html',
			controller: 'AuthController as auth',
			 resolve: {
				loadMyDirectives:function($ocLazyLoad){
					return $ocLazyLoad.load(
					{
						name:'authApp',
						files:[
						 'public/scripts/adminController.js',
						 'public/assets/admin/js/loading.js',
						 'public/scripts/directives/headers/super-admin-header.js',
						 'public/scripts/directives/popup/admin-popup.js',
						 'public/assets/admin/css/custom_admin.css',
						]
					})
				 }
			}
		})	
		.state('admindashboard-payment', {
			url: '/admindashboard/payment/{id}',
			templateUrl: 'public/views/admin/admindashboard.html',
			controller: 'AuthController as auth',
			 resolve: {
				loadMyDirectives:function($ocLazyLoad){
					return $ocLazyLoad.load(
					{
						name:'authApp',
						files:[
						 'public/scripts/adminController.js',
						 'public/assets/admin/js/loading.js',
						 'public/scripts/directives/headers/super-admin-header.js',
						 'public/scripts/directives/popup/admin-popup.js',
						 'public/assets/admin/css/custom_admin.css',
						]
					})
				 }
			}
		})		
		.state('admindashboard-paymenthistory', {
			url: '/admindashboard/paymenthistory/{id}',
			templateUrl: 'public/views/admin/admindashboard.html',
			controller: 'AuthController as auth',
			 resolve: {
				loadMyDirectives:function($ocLazyLoad){
					return $ocLazyLoad.load(
					{
						name:'authApp',
						files:[
						 'public/scripts/adminController.js',
						 'public/assets/admin/js/loading.js',
						 'public/scripts/directives/headers/super-admin-header.js',
						 'public/scripts/directives/popup/admin-popup.js',
						 'public/assets/admin/css/custom_admin.css',
						]
					})
				 }
			}
		})	*/
				.state('admindashboard', {
					abstract:true,
					url: '/admindashboard',
					templateUrl: 'public/views/admin/admindashboard.html',
					controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/scripts/adminController.js',
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/super-admin-header.js',
								 'public/scripts/directives/popup/admin-popup.js',
								 'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				})
				.state('admindashboard.home', {
				 url: '/home',
			
				})
				.state('admindashboard.users', {
					url: '/users',
					
				})
				.state('admindashboard.users-new', {
					url: '/users/new',
					
				})
				.state('admindashboard.users-id', {
					url: '/users/{id}',
					
				})
				.state('admindashboard.payment', {
				 url: '/payment',
			
				})
				.state('admindashboard.payment-new', {
				 url: '/payment/new',
			
				})
				.state('admindashboard.payment-id', {
				 url: '/payment/{id}',
			
				})
				.state('admindashboard.paymenthistory', {
				 url: '/paymenthistory/{id}',
			
				})
				.state('admindashboard.changepassword', {
				 url: '/changepassword',
			
				})
				.state('admindashboard.myprofile', {
				 url: '/myprofile',
			
				})
				
				
				
		
			.state('myfunnels', {   											
				    url: '/myfunnels',
				    templateUrl: 'public/views/admin/my-funnels.html',
				    controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/login-after.js',
								 'public/scripts/directives/popup/admin-popup.js',
								'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				   })
				    .state('editmyfunnel', {   											
				    url: '/editmyfunnel/{id}',
				    templateUrl: 'public/views/admin/analytics.html',
				    controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/login-after.js',
								 'public/scripts/directives/sidebar/funnel-step-sidebar.js',
								 'public/scripts/directives/popup/admin-popup.js',
								'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				   })
				   .state('funnelscontacts', {   											
				    url: '/funnelscontacts/{id}',
				    templateUrl: 'public/views/admin/funnels-contact.html',
				    controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/assets/admin/js/loading.js',
								 'public/scripts/directives/headers/login-after.js',
								 'public/scripts/directives/popup/admin-popup.js',
								'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				   })
				   .state('funnelsettings', {   											
				    url: '/funnelsettings/{id}',
				    templateUrl: 'public/views/admin/funnel-settings.html',
				    controller: 'AuthController as auth',
					 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								 'public/scripts/directives/headers/login-after.js',
								'public/assets/admin/css/custom_admin.css',
								]
							})
						 }
					}
				   })
			.state('facebook-tab', {
				url: '/facebook-tab/{id}',
				cache: false,
				params: {reload: false},
				templateUrl: 'public/views/admin/template-main.html',
				controller: 'AuthController as auth',
				templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
	     		  {
	     			  console.log(stateParams);
	     			  console.log(templateRequest);
	     		  }
	     		],
				 resolve: {
					loadMyDirectives:function($ocLazyLoad){
						return $ocLazyLoad.load(
						{
							name:'authApp',
							files:[
							       	'public/scripts/funnelController.js',
							        'public/assets/admin/js/loading.js',
									 'public/scripts/directives/headers/header.js',
									 'public/scripts/directives/sidebar/sidebar.js',
									 'public/scripts/directives/custom/custom-setting.js',
									 'public/scripts/directives/popup/popup.js',
									 'public/scripts/directives/templates/template.js',
									 'public/assets/admin/css/custom_admin.css',
									
									 'public/assets/template/facebook-tab/css/custom_fb.css',
									 
						
							]
						})
					 }
				}
			})
			.state('account', {
				url: '/mysettings/{id}',
				templateUrl: 'public/views/admin/mysettings.html',
				controller: 'AuthController as auth',
				 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								   	'public/scripts/directives/headers/login-after.js',
								    'public/assets/admin/css/custom_admin.css',
								    'public/scripts/directives/popup/admin-popup.js',
								   
								
								]
							})
						 }
					}
							
			})
			.state('account.autoresponder', {
				url: '/autoresponder',
				 cache: false,
				templateUrl: 'public/views/admin/mysettings.html',
				controller: 'AuthController as auth',
				 resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								   	'public/scripts/directives/headers/login-after.js',
								    'public/assets/admin/css/custom_admin.css',
								    'public/scripts/directives/popup/admin-popup.js',
								
								]
							})
						 }
					}
							
			})
			.state('launching-soon', {
			    url: '/launching-soon/{id}',
			    cache: false,
			    params: {reload: false},
			    templateUrl: 'public/views/admin/template-main.html',
			    controller: 'AuthController as auth',
			    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
			          {
			           console.log(stateParams);
			           console.log(templateRequest);
			          }
			        ],
			     resolve: {
			     loadMyDirectives:function($ocLazyLoad){
			      return $ocLazyLoad.load(
			      {
			       name:'authApp',
			       files:[
			        'public/scripts/funnelController.js',
			        'public/assets/admin/js/loading.js',
			        'public/scripts/directives/custom/datetime_picker.js',
			        'public/scripts/directives/headers/header.js',
			        'public/scripts/directives/sidebar/sidebar.js',
			        'public/scripts/directives/custom/custom-setting.js',
			        'public/scripts/directives/popup/popup.js',
			        'public/scripts/directives/templates/launching-soon.js',
			        'public/assets/admin/css/custom_admin.css',
			        'public/assets/template/launching-soon/css/custom_ls.css',
			       
			       ]
			      })
			      }
			    }
   }).state('thank-you', {
	    url: '/thank-you/{id}',
	    cache: false,
	    params: {reload: false},
	    templateUrl: 'public/views/admin/template-main.html',
	    controller: 'AuthController as auth',
	    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
	          {
	           console.log(stateParams);
	           console.log(templateRequest);
	          }
	        ],
	     resolve: {
	     loadMyDirectives:function($ocLazyLoad){
	      return $ocLazyLoad.load(
	      {
	       name:'authApp',
	       files:[
	       'public/scripts/funnelController.js',
	       'public/assets/admin/js/loading.js',
	        'public/scripts/directives/headers/header.js',
	        'public/scripts/directives/sidebar/sidebar.js',
	        'public/scripts/directives/custom/custom-setting.js',
	        'public/scripts/directives/popup/popup.js',
	        'public/scripts/directives/templates/thank-you.js',
	        'public/assets/template/thank-you/css/custom_tk.css',
	        'public/assets/admin/css/custom_admin.css',
	        
	       ]
	      })
	      }
	    }
	   }).state('optin', {
	    url: '/optin/{id}',
	    templateUrl: 'public/views/admin/template-main.html',
	    controller: 'AuthController as auth',
	    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
	          {
	           console.log(stateParams);
	           console.log(templateRequest);
	          }
	        ],
	     resolve: {
	     loadMyDirectives:function($ocLazyLoad){
	      return $ocLazyLoad.load(
	      {
	       name:'authApp',
	       files:[
	       'public/scripts/funnelController.js',
	       'public/assets/admin/js/loading.js',
	        'public/scripts/directives/headers/header.js',
	        'public/scripts/directives/sidebar/sidebar.js',
	        'public/scripts/directives/custom/custom-setting.js',
	        'public/scripts/directives/popup/popup.js',
	        'public/scripts/directives/templates/optin.js',
	        'public/assets/template/optin/css/custom_optin.css',
	        'public/assets/admin/css/custom_admin.css',
	        
	       ]
	      })
	      }
	    }
	   }).state('videofunnel-salesvideo', {
		    url: '/videofunnel-salesvideo/{id}',
		    templateUrl: 'public/views/admin/template-main.html',
		    controller: 'AuthController as auth',
		    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
		          {
		           console.log(stateParams);
		           console.log(templateRequest);
		          }
		        ],
		     resolve: {
		     loadMyDirectives:function($ocLazyLoad){
		      return $ocLazyLoad.load(
		      {
		       name:'authApp',
		       files:[
		       'public/scripts/funnelController.js',
		       'public/assets/admin/js/loading.js',
		        'public/scripts/directives/headers/header.js',
		        'public/scripts/directives/sidebar/sidebar.js',
		        'public/scripts/directives/custom/custom-setting.js',
		        'public/scripts/directives/popup/popup.js',
		        'public/scripts/directives/templates/videofunnel-salesvideo.js',
		        'public/assets/template/videofunnel-salesvideo/css/custom_vfsv.css',
		        'public/assets/admin/css/custom_admin.css',
		        
		       ]
		      })
		      }
		    }
		   }).state('addtocart', {
			    url: '/addtocart/{id}',
			    templateUrl: 'public/views/admin/template-main.html',
			    controller: 'AuthController as auth',
			    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
			          {
			           console.log(stateParams);
			           console.log(templateRequest);
			          }
			        ],
			     resolve: {
			     loadMyDirectives:function($ocLazyLoad){
			      return $ocLazyLoad.load(
			      {
			       name:'authApp',
			       files:[
			       'public/scripts/funnelController.js',
			       'public/assets/admin/js/loading.js',
			        'public/scripts/directives/headers/header.js',
			        'public/scripts/directives/sidebar/sidebar.js',
			        'public/scripts/directives/custom/custom-setting.js',
			        'public/scripts/directives/popup/popup.js',
			        'public/scripts/directives/templates/addtocart.js',
			        'public/assets/template/addtocart/css/custom_addtocart.css',
			        'public/assets/admin/css/custom_admin.css',
			        
			       ]
			      })
			      }
			    }
			   }).state('videofunnel', {
				    url: '/videofunnel/{id}',
				    templateUrl: 'public/views/admin/template-main.html',
				    controller: 'AuthController as auth',
				    templateProvider: ['$stateParams', '$templateRequest',function(stateParams, templateRequest) 
				          {
				           console.log(stateParams);
				           console.log(templateRequest);
				          }
				        ],
				     resolve: {
				     loadMyDirectives:function($ocLazyLoad){
				      return $ocLazyLoad.load(
				      {
				       name:'authApp',
				       files:[
				       'public/scripts/funnelController.js',
				       'public/assets/admin/js/loading.js',
				        'public/scripts/directives/headers/header.js',
				        'public/scripts/directives/sidebar/sidebar.js',
				        'public/scripts/directives/custom/custom-setting.js',
				        'public/scripts/directives/popup/popup.js',
				        'public/scripts/directives/templates/videofunnel.js',
				        'public/assets/template/videofunnel/css/custom_videofunnel.css',
				        'public/assets/admin/css/custom_admin.css',
				        
				       ]
				      })
				      }
				    }
				   }).state('preview/facebook-tab', {   											
		    url: '/preview/facebook-tab/{id}',
		    controller: 'AuthController as auth',
		    templateUrl: 'public/scripts/directives/preview-templates/static-preview/facebook-tab.html',
		   })
		   .state('preview/launching-soon', {   											
			    url: '/preview/launching-soon/{id}',
			    controller: 'AuthController as auth',
			    templateUrl: 'public/scripts/directives/preview-templates/static-preview/launching-soon.html',
			 })
			 .state('preview/thank-you', {   											
				    url: '/preview/thank-you/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/static-preview/thank-you.html',
				   })

			 .state('preview/addtocart', {   											
				    url: '/preview/addtocart/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/static-preview/addtocart.html',
				   })
			 .state('preview/optin', {   											
				    url: '/preview/optin/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/static-preview/optin.html',
				   })
			 .state('preview/videofunnel', {   											
				    url: '/preview/videofunnel/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/static-preview/videofunnel.html',
				   })
			 .state('preview/videofunnel-salesvideo', {   											
				    url: '/preview/videofunnel-salesvideo/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/static-preview/videofunnel-salesvideo.html',
				   });
			 $stateProvider.state('5850', {   											
				    url: '/5850/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/facebook-tab.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',
								       'public/assets/admin/js/loading.js',
									'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5851', {   											
				    url: '/5851/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/launching-soon.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5852', {   											
				    url: '/5852/{id}',
				   templateUrl: 'public/scripts/directives/preview-templates/thank-you.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',  
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5860', {   											
				    url: '/5860/{id}',
				  templateUrl: 'public/scripts/directives/preview-templates/addtocart.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/assets/admin/js/loading.js',
								       'public/scripts/previewController.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5861', {   											
				    url: '/5861/{id}',
				   templateUrl: 'public/scripts/directives/preview-templates/optin.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5862', {   											
				    url: '/5862/{id}',
				   templateUrl: 'public/scripts/directives/preview-templates/videofunnel.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			 $stateProvider.state('5863', {   											
				    url: '/5863/{id}',
				    templateUrl: 'public/scripts/directives/preview-templates/videofunnel-salesvideo.html',
				    resolve: {
						loadMyDirectives:function($ocLazyLoad){
							return $ocLazyLoad.load(
							{
								name:'authApp',
								files:[
								       'public/scripts/previewController.js',
								       'public/assets/admin/js/loading.js',
								       'public/scripts/directives/popup/preview-popup.js',
									]
							})
						 }
					},
					
				   });
			
			
					
		});
		

//})();
