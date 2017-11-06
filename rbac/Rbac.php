<?php

//http://www.cnblogs.com/lwn007/p/6679095.html
class Rbac
{
    static public function AccessDecision($appName=APP_NAME) {
        //检查是否需要认证
        if(RBAC::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid   =   md5($appName.MODULE_NAME.ACTION_NAME);
            if(emptyempty($_SESSION[C('ADMIN_AUTH_KEY')])) {
                if(C('USER_AUTH_TYPE')==2) $accessList = RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);   //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效   //通过数据库进行访问检查
                else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if( $_SESSION[$accessGuid]) return true;

                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = $_SESSION['_ACCESS_LIST'];
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                $module = defined('P_MODULE_NAME')?  P_MODULE_NAME   :   MODULE_NAME;
                if(!isset($accessList[strtoupper($appName)][strtoupper($module)][strtoupper(ACTION_NAME)])) {
                    $_SESSION[$accessGuid]  =   false;
                    return false;
                }
                else $_SESSION[$accessGuid]  =   true;

            }else return true;

            //管理员无需认证
        }
        return true;
    }
}