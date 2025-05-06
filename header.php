<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<body>
	<header>
		<div class="menu">
			<div class="title">
                <?php //$this->options->title() ?>
            </div>
			<div class="pages">
                <a href="<?php $this->options->siteUrl(); ?>">主页</a>   
            <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
            <?php while($pages->next()): ?>
                <a <?php if($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?> href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
            <?php endwhile; ?>	
			</div>
		</div>		
		<div class='theme-toggle'>🌓</div>
	</header>