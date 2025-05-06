<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
//主题设置
function themeConfig($form) {
    $icoUrl = new Typecho_Widget_Helper_Form_Element_Text('icoUrl', NULL, NULL, _t('站点 Favicon 地址'));
    $form->addInput($icoUrl);
    $sticky = new Typecho_Widget_Helper_Form_Element_Text('sticky', NULL, NULL, _t('置顶文章cid'), _t('多篇文章以`|`符号隔开'), _t('会在首页展示置顶文章。'));
    $form->addInput($sticky);
    $showtime = new Typecho_Widget_Helper_Form_Element_Radio('showtime',
    array('0'=> _t('否'), '1'=> _t('是')),
    '0', _t('是否显示页面加载时间'), _t('选择“是”将在页脚显示加载时间。'));
    $form->addInput($showtime);
    $cnavatar = new Typecho_Widget_Helper_Form_Element_Text('cnavatar', NULL, NULL , _t('Gravatar镜像'), _t('默认https://cravatar.cn/avatar/'));
    $form->addInput($cnavatar);
    $addhead = new Typecho_Widget_Helper_Form_Element_Textarea('addhead', NULL, NULL, _t('Head内代码用于网站验证等'), _t('支持HTML'));
    $form->addInput($addhead);
    $tongji = new Typecho_Widget_Helper_Form_Element_Textarea('tongji', NULL, NULL, _t('统计代码'), _t('支持HTML'));
    $form->addInput($tongji);
}

function saveThemeConfig($config) {
    // 可以在这里添加额外的验证或处理逻辑
    return $config;
}

/** 头像镜像     */
$options = Typecho_Widget::widget('Widget_Options');
$gravatarPrefix = empty($options->cnavatar) ? 'https://cravatar.cn/avatar/' : $options->cnavatar;
define('__TYPECHO_GRAVATAR_PREFIX__', $gravatarPrefix);

/**
* 页面加载时间
*/
function timer_start() {
    global $timestart;
    $mtime = explode( ' ', microtime() );
    $timestart = $mtime[1] + $mtime[0];
    return true;
    }
    timer_start();
    function timer_stop( $display = 0, $precision = 3 ) {
    global $timestart, $timeend;
    $mtime = explode( ' ', microtime() );
    $timeend = $mtime[1] + $mtime[0];
    $timetotal = number_format( $timeend - $timestart, $precision );
    $r = $timetotal < 1 ? $timetotal * 1000 . " ms" : $timetotal . " s";
    if ( $display ) {
    echo $r;
    }
    return $r;
    }

//回复加上@
function getPermalinkFromCoid($coid) {
	$db = Typecho_Db::get();
	$row = $db->fetchRow($db->select('author')->from('table.comments')->where('coid = ? AND status = ?', $coid, 'approved'));
	if (empty($row)) return '';
	return '<a href="#comment-'.$coid.'" style="text-decoration: none;">@'.$row['author'].'</a>';
}

 
/**    
 * 评论者认证等级 + 身份    
 *    
 * @author Chrison    
 * @access public    
 * @param str $email 评论者邮址    
 * @return result     
 */     
function commentApprove($widget, $email = NULL)      
{   
    $result = array(
        "state" => -1,//状态
        "isAuthor" => 0,//是否是博主
        "userLevel" => '',//用户身份或等级名称
        "userDesc" => '',//用户title描述
        "bgColor" => '',//用户身份或等级背景色
        "commentNum" => 0//评论数量
    );
    if (empty($email)) return $result;      
    $result['state'] = 1;     
    if ($widget->authorId == $widget->ownerId) {      
        $result['isAuthor'] = 1;
        $result['userLevel'] = '作者';
        $result['userDesc'] = '博主';
        $result['bgColor'] = '#FFD700';
        $result['commentNum'] = 999;
    } else {
        $db = Typecho_Db::get();
        $commentNumSql = $db->fetchAll($db->select(array('COUNT(cid)'=>'commentNum'))
            ->from('table.comments')
            ->where('mail = ?', $email));
        $commentNum = $commentNumSql[0]['commentNum'];
        //$linkSql = $db->fetchAll($db->select()->from('table.links')
        //    ->where('user = ?',$email));
        if($commentNum==1){
            $result['userLevel'] = '初识';
            $result['bgColor'] = '#999999';
            $userDesc = '初来乍到的新朋友';
        } else {
            if ($commentNum<3 && $commentNum>1) {
                $result['userLevel'] = '初识';
                $result['bgColor'] = '#999999';
            }elseif ($commentNum<9 && $commentNum>=3) {
                $result['userLevel'] = '朋友';
                $result['bgColor'] = '#A0DAD0';
            }elseif ($commentNum<27 && $commentNum>=9) {
                $result['userLevel'] = '好友';
                $result['bgColor'] = '#FF8C00';
            }elseif ($commentNum<81 && $commentNum>=27) {
                $result['userLevel'] = '挚友';
                $result['bgColor'] = '#FF0000';
            }elseif ($commentNum<100 && $commentNum>=81) {
                $result['userLevel'] = '兄弟';
                $result['bgColor'] = '#006400';
            }elseif ($commentNum>=100) {
                $result['userLevel'] = '老铁';
                $result['bgColor'] = '#A0DAD0';
            }
             $userDesc = '已有'.$commentNum.'条评论'; 
        }
       // if($linkSql){
        //    $result['userLevel'] = '博友';
        //    $result['bgColor'] = '#21b9bb';
        //    $userDesc = '🔗'.$linkSql[0]['description'].'&#10;✌️'.$userDesc;
       // }       
        $result['userDesc'] = $userDesc;
        $result['commentNum'] = $commentNum;
    } 
    return $result;
}

/**
 * 将时间戳转换为“多久之前”的格式
 *
 * @param int $timestamp 时间戳
 * @return string
 */
function time_ago($timestamp) {
    $current_time = time();
    $time_diff = $current_time - $timestamp;

    if ($time_diff < 60) {
        return $time_diff . ' 秒前';
    } elseif ($time_diff < 3600) {
        return floor($time_diff / 60) . ' 分钟前';
    } elseif ($time_diff < 86400) {
        return floor($time_diff / 3600) . ' 小时前';
    } elseif ($time_diff < 2592000) {
        return floor($time_diff / 86400) . ' 天前';
    } elseif ($time_diff < 31536000) {
        return floor($time_diff / 2592000) . ' 个月前';
    } else {
        return floor($time_diff / 31536000) . ' 年前';
    }
}

/**
 * 豆瓣卡片渲染
 * 
 */
function renderDoubanCards($content) {
    $pattern = '/<a[^>]+href=["\'](https?:\/\/(movie|book)\.douban\.com\/subject\/(\d+)\/?)["\'][^>]*>(.*?)<\/a>/i';
    return preg_replace_callback($pattern, function($matches) {
        $url = $matches[1];
        $type = $matches[2];
        $id = $matches[3];
        $title = $matches[4];
        // 你的第三方API，需保证可用
        $apiBase = 'https://api.loliko.cn/';
        if ($type === 'movie') {
            $apiUrl = $apiBase . "movies/" . $id;
            $resp = @file_get_contents($apiUrl);
            if (!$resp) return $matches[0]; // API请求失败则返回原链接
            $data = json_decode($resp, true);
            if (empty($data) || !isset($data['name'])) return $matches[0];
            $star = ceil($data['rating']);
            $card = "<div class='post-preview'><div class='post-preview--meta'><div class='post-preview--middle'><h4 class='post-preview--title'><a target='_blank' rel='noreferrer' href='{$url}'>《{$data['name']}》</a></h4><div class='rating'><div class='rating-star allstar{$star}'></div><div class='rating-average'>{$data['rating']}</div></div><time class='post-preview--date'>导演：{$data['director']} / 类型：{$data['genre']} / {$data['year']}</time><section class='post-preview--excerpt'>" . preg_replace('/\s*/u', '', $data['intro']) . "</section></div></div><img referrer-policy='no-referrer' loading='lazy' class='post-preview--image' src='{$data['img']}'></div>";
            return $card;
        } elseif ($type === 'book') {
            $apiUrl = $apiBase . "v2/book/id/" . $id;
            $resp = @file_get_contents($apiUrl);
            if (!$resp) return $matches[0];
            $data = json_decode($resp, true);
            if (empty($data) || !isset($data['title'])) return $matches[0];
            $star = ceil($data['rating']['average']);
            $card = "<div class='post-preview'><div class='post-preview--meta'><div class='post-preview--middle'><h4 class='post-preview--title'><a target='_blank' rel='noreferrer' href='{$url}'>《{$data['title']}》</a></h4><div class='rating'><div class='rating-star allstar{$star}'></div><div class='rating-average'>{$data['rating']['average']}</div></div><time class='post-preview--date'>作者：".(is_array($data['author']) ? implode(' / ', $data['author']) : $data['author'])."</time><section class='post-preview--excerpt'>" . preg_replace('/\s*/u', '', $data['summary']) . "</section></div></div><img referrer-policy='no-referrer' loading='lazy' class='post-preview--image' src='{$data['images']['medium']}'></div>";
            return $card;
        }
        return $matches[0];
    }, $content);
}

// --- 内容处理工具函数 ---
function clean_content($content) {
    // 1. 移除所有 <img> 和 <br> 标签
    $content = preg_replace('/<img[^>]*>/i', '', $content);
    $content = preg_replace('/<br\s*\/?>/i', '', $content);
    // 2. 媒体替换
    $mediaPatterns = [
    // Bilibili
    '/<a\s+href="https?:\/\/www\.bilibili\.com\/video\/((av\d{1,10})|(BV[\w]{10}))\/?".*?<\/a>/i' => 
    '<div class="video-wrapper"><iframe src="//www.bilibili.com/blackboard/html5mobileplayer.html?bvid=$1&as_wide=1" frameborder="0" allowfullscreen></iframe></div>',
    // YouTube
    '/<a\s+href="https?:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})".*?<\/a>/i' => 
    '<div class="video-wrapper"><iframe src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>',
    // 网易云音乐
    '/<a\s+href="https?:\/\/music\.163\.com\/.*id=(\d+)".*?<\/a>/i' => 
    '<meting-js auto="https://music.163.com/#/song?id=$1"></meting-js>',
    // QQ音乐
    '/<a\s+href="https?:\/\/y\.qq\.com\/.*\/([a-zA-Z0-9]+)(\.html)?".*?<\/a>/i' => 
    '<meting-js auto="https://y.qq.com/n/yqq/song/$1.html"></meting-js>',
    // 腾讯视频
    '/<a\s+href="https?:\/\/v\.qq\.com\/.*\/([a-zA-Z0-9]+)\.html".*?<\/a>/i' => 
    '<div class="video-wrapper"><iframe src="//v.qq.com/iframe/player.html?vid=$1" frameborder="0" allowfullscreen></iframe></div>',
    // Spotify
    '/<a\s+href="https?:\/\/open\.spotify\.com\/(track|album)\/([a-zA-Z0-9]+)".*?<\/a>/i' => 
    '<div class="spotify-wrapper"><iframe src="https://open.spotify.com/embed/$1/$2" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></div>',
    // 优酷视频
    '/<a\s+href="https?:\/\/v\.youku\.com\/.*\/id_([a-zA-Z0-9=]+)\.html".*?<\/a>/i' => 
    '<div class="video-wrapper"><iframe src="https://player.youku.com/embed/$1" frameborder="0" allowfullscreen></iframe></div>'
    ];
    foreach ($mediaPatterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    return $content;
}

// --- 获取缩略图函数 ---
$theme_dir = basename(dirname(__FILE__));
// 缩略图生成函数
function get_thumb($imgUrl, $theme_dir) {
    $upload_dir = __DIR__ . '/thumbnails/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    // 生成缩略图文件名
    $hash = md5($imgUrl);
    $thumbnail_path = $upload_dir . $hash . '.jpg';
    $thumbnail_url = Helper::options()->themeUrl . '/thumbnails/' . $hash . '.jpg';
    // 已经存在直接返回
    if (file_exists($thumbnail_path)) {
        return $thumbnail_url;
    }
    // 支持外链图片，下载到本地
    $img_data = @file_get_contents($imgUrl);
    if ($img_data === false) return $imgUrl; // 网络图片无法拉取直接返回原图
    $src = @imagecreatefromstring($img_data);
    if (!$src) return $imgUrl;
    $width = imagesx($src);
    $height = imagesy($src);
    $min = max(300, min($width, $height));
    $size = $min;
    // 居中裁剪
    $src_x = ($width - $size) / 2;
    $src_y = ($height - $size) / 2;
    $thumb = imagecreatetruecolor($size, $size);
    imagecopyresampled($thumb, $src, 0, 0, $src_x, $src_y, $size, $size, $size, $size);
    // 保存缩略图
    imagejpeg($thumb, $thumbnail_path, 90);

    imagedestroy($src);
    imagedestroy($thumb);
    return $thumbnail_url;
}

/**
 * 修改附件插入功能
 */
// 添加批量插入按钮的JavaScript
Typecho_Plugin::factory('admin/write-post.php')->bottom = array('MyHelper', 'addBatchInsertButton');
Typecho_Plugin::factory('admin/write-page.php')->bottom = array('MyHelper', 'addBatchInsertButton');

class MyHelper {
    public static function addBatchInsertButton() {
        ?>
        <script>
        $(document).ready(function() {
            // 添加批量插入按钮
            var batchButton = $('<button type="button" class="btn primary" id="batch-insert">批量插入所有附件</button>');
            $('#file-list').before(batchButton);
            
            // 修改单个附件的插入格式
            Typecho.insertFileToEditor = function(title, url, isImage) {
                var textarea = $('#text'), sel = textarea.getSelection(),
                    insertContent = isImage ? '![' + title + '](' + url + ')' : 
                                            '[' + title + '](' + url + ')';
                
                textarea.replaceSelection(insertContent);
                textarea.focus();
            };
            
            // 批量插入功能
            $('#batch-insert').click(function() {
                var content = '';
                $('#file-list li').each(function() {
                    var $this = $(this),
                        title = $this.find('.insert').text(),
                        url = $this.data('url'),
                        isImage = $this.data('image') == 1;
                        
                    content += isImage ? '![' + title + '](' + url + ')\n' : 
                                       '[' + title + '](' + url + ')\n';
                });
                
                var textarea = $('#text');
                var pos = textarea.getSelection();
                var newContent = textarea.val();
                if (pos.start === pos.end) {
                    newContent = newContent.substring(0, pos.start) + content + newContent.substring(pos.start);
                } else {
                    newContent = newContent.substring(0, pos.start) + content + newContent.substring(pos.end);
                }
                textarea.val(newContent);
                textarea.focus();
            });
        });
        </script>
        <?php
    }
}