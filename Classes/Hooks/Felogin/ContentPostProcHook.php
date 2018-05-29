<?php

namespace GeorgRinger\GoogleSignin\Hooks\Felogin;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Felogin\Controller\FrontendLoginController;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ContentPostProcHook
{

    /** @var ExtensionConfiguration */
    protected $extensionConfiguration;

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function run(&$params, FrontendLoginController $frontendLoginController)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addHeaderData(
            '<meta name="google-signin-client_id" content="' . $this->extensionConfiguration->getClientId() . '">'
        );
        $content = $params['content'];

        $view = GeneralUtility::makeInstance(StandaloneView::class);
//        $view->getRequest()->setControllerExtensionName('google_login');
//        $view->setTemplateSource(file_get_contents(GeneralUtility::getFileAbsFileName('EXT:google_signin/Resources/Private/Templates/Frontend.html')));
//
//        $content .= $view->render();


        $additionalContent = ' <div class="pull-right" id="my-signin2"></div>

            <input type="hidden" id="google-token" name="google-token" value=""/>
            
            
             <div class="row" id="google-infos" style="display: none">
                <div class="col-xs-5">
                    <img id="profileImage" src="" class="img-responsive img-circle"/>
                </div>
                <div class="col-xs-7">
                    <div id="google-info-text"></div>
                    <div id="google-info-links">
                     <input type="checkbox" name="google-signin" value="1"/>
                        <a href="#" onclick="signOut();" style="display: none">Sign out</a>
                        <a href="#" class="btn btn-info" onclick="disconnect();">Disconnect</a>
                    </div>
                </div>
            </div>
            <hr>
           
            <script>
                function onSuccess(googleUser) {
                    var profile = googleUser.getBasicProfile();
                    setProfileImage(profile.getImageUrl());
                    setProfileText(profile);
                    document.getElementById(\'google-token\').value = googleUser.getAuthResponse().id_token;
                    document.getElementById(\'google-infos\').removeAttribute(\'style\');
                    document.getElementById(\'google-info-links\').removeAttribute(\'style\');
                }

                function setProfileText(profile) {
                    var message = \'<h4>\' + profile.getName() + \'</h4>\'
                        + profile.getEmail();

                    document.getElementById("google-info-text").innerHTML = message;
                }

                function onFailure(error) {
                    console.log(error);
                }

                function renderButton() {
                    gapi.signin2.render(\'my-signin2\', {
                        \'scope\': \'profile email\',
                        // \'width\': 240,
                        // \'height\': 50,
                        \'longtitle\': false,
                        \'theme\': \'dark\',
                        \'onsuccess\': onSuccess,
                        \'onfailure\': onFailure
                    });
                }

                function setProfileImage(srcUrl) {
                    var element = document.getElementById("profileImage");
                    if (srcUrl == null) {
                        element.style.display = "none";
                        element.src = "";
                    } else {
                        element.style.display = "block";
                        element.src = srcUrl;
                    }
                }

                function signOut() {
                    var auth2 = gapi.auth2.getAuthInstance();
                    auth2.signOut().then(function () {
                        // console.log(\'User signed out.\');
                        document.getElementById("google-info-text").innerHTML = \'\';
                        document.getElementById("google-info-links").style.display = "none";
                        document.getElementById("profileImage").style.display = "none";
                    });
                }

                function disconnect() {
                    var auth2 = gapi.auth2.getAuthInstance();
                    auth2.disconnect().then(function () {
                        // console.log(\'User signed out.\');
                        document.getElementById("google-info-text").innerHTML = \'\';
                        document.getElementById("google-info-links").style.display = "none";
                        document.getElementById("profileImage").style.display = "none";
                    });
                }
                
                function loadSrc() {
                    var script = document.createElement(\'script\');
                    script.onload = function () {
                        //do stuff with the script
                    };
                    script.src = "https://apis.google.com/js/platform.js?onload=renderButton";
                    
                    document.head.appendChild(script)
                }
                </script>
            ';


        $searchReplace = [
            '###GOOGLE_SIGNIN_SWITCH_LINK###' => '<a href="javascript:loadSrc();">try google login</a>',
            '###GOOGLE_SIGNIN_CONTENT###' => $additionalContent
        ];


        $content = str_replace(array_keys($searchReplace), array_values($searchReplace), $content);
        return $content;
    }
}