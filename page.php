<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('head.php');?>
<?php $this->need('header.php');?>
<?php
$db = Typecho_Db::get();
$user = Typecho_Widget::widget('Widget_User');
if ($user->hasLogin()) {
    $targetUser = $user;
    $userId = $user->uid;
} else {
    try {
        $adminUser = $db->fetchRow($db->select()
            ->from('table.users')
            ->where('group = ?', 'administrator')
            ->limit(1));
        if ($adminUser) {
            // 使用管理员信息创建临时用户对象
            $targetUser = new stdClass();
            $targetUser->uid = $adminUser['uid'];
            $targetUser->mail = $adminUser['mail'];
            $targetUser->screenName = $adminUser['screenName'];
            $userId = $adminUser['uid'];
        } else {
            // 如果找不到管理员，返回空
            echo "";
            return;
        }
    } catch (Exception $e) {
        echo "";
        return;
    }
}
// 生成 Gravatar 头像 URL
$email = $targetUser->mail;
$options = Typecho_Widget::widget('Widget_Options');
$gravatarPrefix = empty($options->cnavatar) ? 'https://cravatar.cn/avatar/' : $options->cnavatar;
$gravatarUrl = $gravatarPrefix . md5(strtolower(trim($email))) . '?s=80&d=mm&r=g';
$gravatarUrl2x = $gravatarPrefix . md5(strtolower(trim($email))) . '?s=160&d=mm&r=g';
?>
    <section id="main" class="container">
		<h1><?php $this->title() ?></h1>
		<blockquote>
			<p><?php echo $this->getDescription(); ?>  </p>
		</blockquote>
	 
		<div id="memos" class="memos">
        <ul class="">
        <?php
        $content = $this->content;
        preg_match_all('/<img[^>]+src="([^">]+)"/i', $content, $matches);
        $images = $matches[1];
        $cleanHtml = clean_content($content);
        $cleanHtml = renderDoubanCards($cleanHtml);
    ?>
        <li class="timeline">
            <div class="memos__content" style="--avatar-url: url(<?php echo htmlspecialchars($gravatarUrl); ?>)">
                <div class="memos__text">
                    <div class="memos__userinfo">
                        <div>                    
                        <a href="<?php $this->author->permalink(); ?>"><?php $this->author(); ?></a>
                        </div>
                        <svg viewBox="0 0 24 24" aria-label="认证账号" class="memos__verify"><g><path d="M22.5 12.5c0-1.58-.875-2.95-2.148-3.6.154-.435.238-.905.238-1.4 0-2.21-1.71-3.998-3.818-3.998-.47 0-.92.084-1.336.25C14.818 2.415 13.51 1.5 12 1.5s-2.816.917-3.437 2.25c-.415-.165-.866-.25-1.336-.25-2.11 0-3.818 1.79-3.818 4 0 .494.083.964.237 1.4-1.272.65-2.147 2.018-2.147 3.6 0 1.495.782 2.798 1.942 3.486-.02.17-.032.34-.032.514 0 2.21 1.708 4 3.818 4 .47 0 .92-.086 1.335-.25.62 1.334 1.926 2.25 3.437 2.25 1.512 0 2.818-.916 3.437-2.25.415.163.865.248 1.336.248 2.11 0 3.818-1.79 3.818-4 0-.174-.012-.344-.033-.513 1.158-.687 1.943-1.99 1.943-3.484zm-6.616-3.334l-4.334 6.5c-.145.217-.382.334-.625.334-.143 0-.288-.04-.416-.126l-.115-.094-2.415-2.415c-.293-.293-.293-.768 0-1.06s.768-.294 1.06 0l1.77 1.767 3.825-5.74c.23-.345.696-.436 1.04-.207.346.23.44.696.21 1.04z"></path></g></svg>
                        <div class="memos__id">@admin</div>
                    </div>
                    <span class='tag-span'>
                    <?php if ($this->tags): ?>
            <?php foreach ($this->tags as $tag): ?>
                <a href="<?php echo $tag['permalink']; ?>">#<?php echo $tag['name']; ?></a> 
            <?php endforeach; ?>
            <?php else: ?>
            <?php endif; ?>
                    </span>
                    <p><?php echo $cleanHtml; ?></p>
                    <div class="resource-wrapper">
                        <div class="images-wrapper"><!-- style="display: flex; flex-wrap: wrap; gap: 10px;" -->
                    <?php if (!empty($images)): ?>
                <?php foreach ($images as $imgUrl): ?>
                    <div class="resimg" ><!-- style="flex: 1 1 calc(33.33% - 10px); overflow: hidden; position: relative; height: 200px;" -->
                    <a target="_blank" rel="noreferrer">
                        <img loading="lazy" src="<?php echo $imgUrl; ?>" alt="文章图片"  />
                    </a>               
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
                </div>
                </div>
                </div>
                <div class="memos__meta">
                <small class="memos__date">
                <?php echo time_ago($this->created); ?> 
                </small>
                </div>
            </div>
        </li>
	    </ul>
        </div>
	</section>
<?php $this->need('footer.php'); ?>