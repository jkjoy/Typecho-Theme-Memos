<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php  
$sticky = $this->options->sticky;
$db = Typecho_Db::get();
$pageSize = $this->options->pageSize;
if ($sticky && !empty(trim($sticky))) {
 $sticky_cids = array_filter(explode('|', $sticky));
 if (!empty($sticky_cids)) {
     $sticky_html = " <span class='sticky--post'><svg xmlns='http://www.w3.org/2000/svg' width='16px' height='16px' fill='none' viewBox='0 0 24 24' class='bk'>
<path fill='#242424' fill-rule='evenodd' d='M12.333 16.993a7.4 7.4 0 0 1-1.686-.12 7.25 7.25 0 1 1 8.047-4.334v.001a7.2 7.2 0 0 1-.632 1.188 7.26 7.26 0 0 1-4.708 3.146l-.07.013q-.466.083-.951.105m.356.979a8.4 8.4 0 0 1-1.377 0l-2.075 5.7a.375.375 0 0 1-.625.13l-2.465-2.604-3.563.41a.375.375 0 0 1-.395-.501l2.645-7.267a8.25 8.25 0 1 1 14.333 0l2.645 7.267a.375.375 0 0 1-.396.5l-3.562-.41-2.465 2.604a.375.375 0 0 1-.625-.13zm5.786-3.109a8.25 8.25 0 0 1-4.775 2.962l1.658 4.554 1.77-1.87.344-.362.496.057 2.558.294zm-12.95 0L3.476 20.5l2.557-.295.497-.057.344.363 1.77 1.87 1.658-4.555a8.25 8.25 0 0 1-4.775-2.961' clip-rule='evenodd'></path></svg></span> ";
     
     // 保存原始对象状态
     $originalRows = $this->row;
     $originalStack = $this->stack;
     $originalLength = $this->length;
     $totalOriginal = $this->getTotal();
     
     // 重置当前对象状态
     $this->row = [];
     $this->stack = [];
     $this->length = 0;
     
     if (isset($this->currentPage) && $this->currentPage == 1) {
         // 查询置顶文章
         $selectSticky = $this->select()->where('type = ?', 'post');
         foreach ($sticky_cids as $i => $cid) {
             if ($i == 0) 
                 $selectSticky->where('cid = ?', $cid);
             else 
                 $selectSticky->orWhere('cid = ?', $cid);
         }
         $stickyPosts = $db->fetchAll($selectSticky);
         
         // 添加置顶文章到结果集
         foreach ($stickyPosts as &$stickyPost) {
             $stickyPost['isSticky'] = true;
             $stickyPost['stickyHtml'] = $sticky_html;
             $this->push($stickyPost);
         }
         
         // 计算当前页应显示的普通文章数量
         $standardPageSize = $pageSize - count($stickyPosts);
         
         // 确保第一页不会显示太多文章
         if ($standardPageSize <= 0) {
             $standardPageSize = 0; // 如果置顶文章已经填满或超过一页，则不显示普通文章
         }
     } else {
         // 非第一页显示正常数量的文章
         $standardPageSize = $pageSize;
     }
     
     // 查询普通文章
     if ($this->currentPage == 1) {
         // 第一页需要排除置顶文章并限制数量
         $selectNormal = $this->select()
             ->where('type = ?', 'post')
             ->where('status = ?', 'publish')
             ->where('created < ?', time());
             
         // 排除所有置顶文章
         foreach ($sticky_cids as $cid) {
             $selectNormal->where('table.contents.cid != ?', $cid);
         }
         
         $selectNormal->order('created', Typecho_Db::SORT_DESC)
             ->limit($standardPageSize)
             ->offset(0);
     } else {
         // 非第一页的查询
         // 计算正确的偏移量：(当前页码-1) * 每页数量 - 置顶文章数
         // 这样可以确保不会漏掉文章
         $offset = ($this->currentPage - 1) * $pageSize - count($sticky_cids);
         $offset = max($offset, 0); // 确保偏移量不为负
         
         $selectNormal = $this->select()
             ->where('type = ?', 'post')
             ->where('status = ?', 'publish')
             ->where('created < ?', time());
             
         // 排除所有置顶文章
         foreach ($sticky_cids as $cid) {
             $selectNormal->where('table.contents.cid != ?', $cid);
         }
         
         $selectNormal->order('created', Typecho_Db::SORT_DESC)
             ->limit($pageSize)
             ->offset($offset);
     }
 } else {
     // 没有有效的置顶文章ID，正常查询
     $selectNormal = $this->select()
         ->where('type = ?', 'post')
         ->where('status = ?', 'publish')
         ->where('created < ?', time())
         ->order('created', Typecho_Db::SORT_DESC)
         ->page(isset($this->currentPage) ? $this->currentPage : 1, $pageSize);
 }
} else {
 // 没有设置置顶文章，正常查询
 $selectNormal = $this->select()
     ->where('type = ?', 'post')
     ->where('status = ?', 'publish')
     ->where('created < ?', time())
     ->order('created', Typecho_Db::SORT_DESC)
     ->page(isset($this->currentPage) ? $this->currentPage : 1, $pageSize);
}

// 添加私有文章查询条件
if ($this->user->hasLogin()) {
 $uid = $this->user->uid;
 if ($uid) {
     $selectNormal->orWhere('authorId = ? AND status = ?', $uid, 'private');
 }
}

// 获取普通文章
$normalPosts = $db->fetchAll($selectNormal);

// 如果没有置顶文章或在前面的代码中没有重置对象状态，则在这里重置
if (empty($sticky) || empty(trim($sticky)) || empty($sticky_cids)) {
 $this->row = [];
 $this->stack = [];
 $this->length = 0;
}

// 将普通文章添加到结果集
foreach ($normalPosts as $normalPost) {
 $this->push($normalPost);
}
?>