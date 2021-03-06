<?php
/**
 * 生成后台侧边栏菜单，适配默认后台样式(通过修改这个类适配你自己的后台)
 *
 * Class App_AdminMenu
 */
class App_AdminMenu
{
    /**
     * 适配默认后台样式的菜单列表
     * @param $userInfo
     * @param $currentRequet 当前请求权限节点
     * @return string
     */
    public static function builtMenu($userInfo, $currentRequet)
    {
        //TODO::可以根据业务实现缓存菜单文件
        $rbacManage      = \Core\ServiceLocator::getInstance()->get('rbacManage');
        $itemGroup       = $rbacManage->getItemsGroup('*', null);
        $userPermissionIds = [];
        foreach($rbacManage->userPermissions as $ps){
            $userPermissionIds[] = $ps['item_id'];
        }
        $menuHtml        = '';
        PC::debug($itemGroup);
        foreach ($itemGroup as $item) {
            if(!isset($item['sub'])){
                continue;
            }
            $shtml = '';
            $liActive = '';
            foreach($item['sub'] as $subNode){
                //管理员显示所有菜单
                if( ! $rbacManage->isAdmin($userInfo['name']) ){
                    if( ! in_array($subNode['id'], $userPermissionIds) ){
                        continue;
                    }
                }
                if($subNode['show'] == RbacItemModel::ITEM_SHOW_NOT){
                    continue;
                }
                $subLiActive = '';
                if($currentRequet == strtolower($subNode['title']) ){
                    $liActive = $subLiActive = 'active';
                }
                $shtml .= "<li class='{$subLiActive}'><a href=\"{$subNode['title']}\"><i class=\"fa fa-circle-o text-aqua\"></i><span>{$subNode['desc']}</span></a></li>";
            }

            if(!$shtml){
                continue;
            }
            $menuHtml .= "<li class=\"treeview {$liActive}\">
                            <a href=\"#\"><i class=\"fa fa-th\"></i><span>{$item['desc']}</span><i class=\"fa fa-angle-left pull-right\"></i></a>
                                <ul class=\"treeview-menu\">" . $shtml . "</ul>
                          </li>";
        }

        return $menuHtml;
    }
}