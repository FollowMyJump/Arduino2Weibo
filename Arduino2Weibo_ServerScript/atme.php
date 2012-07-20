<?php
/*
  atme.php - Aruino2Weibo:Remote control your Arduino via weibo.
  Copyright (c) naozhendang.com 2011-2012. 
  
  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 
  More information can be found at:  http://arduino2weibo.sinaapp.com
  
  ****** Server-side Script ******
  ��ȡ@��ǰ�û���΢���б�
*/

// ver0.3 - Control arduino via Weibo
// ver0.2 - Support IDE 1.0
     
header('Content-Type:text/html; charset=utf-8');

//��������΢��API OAuth2.0��֤���ֶ���֮��������
//��������ʹ����Andriodƽ��ͻ��˵�API Key��App Secret
//Arduino��ֱ��ͨ���˻�������������Ȩ,�Ӷ��ܿ�����Ȩҳ��ȱ����΢����Դ����ʾΪAndriodƽ��
//��Ȼ������Ը���ϲ�þ���ʹ�������ͻ��˵�API Key��App Secret��������Google
define( "WB_AKEY" , '2540340328' );
define( "WB_SKEY" , '886cfb4e61fad4e4e9ba9dee625284dd' );


include_once( 'saetv2.ex.class.php' );

if (isset($_REQUEST['username']) && isset($_REQUEST['password']))
{
    //���OAuth2.0 Access Token
    $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
    
            $keys = array();
            $keys['username'] = $_REQUEST['username'];
            $keys['password'] = $_REQUEST['password'];
            try {
                $token = $o->getAccessToken( 'password', $keys ) ;
            } catch (OAuthException $e) {
				echo json_encode(array('error'=>$e->getMessage()));
            }
    
    if ($token) {
    
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token'] );
            
            //���Post�д���since_id
            if(is_numeric($_REQUEST['since_id'])&& !empty($_REQUEST['since_id'])){
            	//��ñ�since_id�����mention
            	//API��{@link http://open.weibo.com/wiki/2/statuses/mentions statuses/mentions}
            	$atme  = $c->mentions(1, NULL,$_REQUEST['since_id'], 0); 
            } else {
            	//û��since_id
            	$atme  = $c->mentions(1, NULL,NULL, 0); 
            }
            
            //��ָ��since_id,�������л�õ�mentions���飬��һ�鼴��since_id�ĺ�һ��
            //��δָ��since_id���Ͳ��õ���ֱ�ӻ�ȡ����һ��
            if(is_numeric($_REQUEST['since_id'])&& !empty($_REQUEST['since_id'])){
            	rsort($atme[statuses],SORT_STRING);
            }
             
            if(isset($atme[error])){
                    echo json_encode($atme);
            } else if(isset($atme[statuses][0])){
            	  //����json
               	  $new_atme = array
                  (
                      'id'=> $atme[statuses][0][id],
                      'text' => $atme[statuses][0][text],       
                      'uid' => $atme[statuses][0][user][id],
                      'user' => $atme[statuses][0][user][screen_name],
                  );      
                  echo json_encode($new_atme);
            } 
    }
}
?>