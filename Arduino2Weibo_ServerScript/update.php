<?php
/*
  update.php - Aruino2Weibo:Remote control your Arduino via weibo.
  Copyright (c) naozhendang.com 2011-2012. 
  
  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 
  More information can be found at:  http://arduino2weibo.sinaapp.com
  
  ****** Server-side Script ******
  ����һ��΢����Ϣ
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

if (isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['status']))
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

	if ($token)
	{
           	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token'] );
                //����һ��΢����Ϣ
                //API��{@link http://open.weibo.com/wiki/2/statuses/update}
          	$msg = $c->update( substr($_REQUEST['status'].date(' Y-n-j H:i:s'),0,139) );
        
                if(isset($msg[error])){
                    echo json_encode($msg);
                } else {
                    //����json
                    $new_msg=array
                    (
                      'time'=> $msg[created_at],
                      'id'=> $msg[id]
                    );      
                    echo json_encode($new_msg);
                }
    }
}
?>