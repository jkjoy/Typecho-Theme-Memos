// Toggle Darkmode
const localTheme = window.localStorage && window.localStorage.getItem("theme");
const themeToggle = document.querySelector(".theme-toggle");

if (localTheme) {
    document.body.classList.remove("light-theme", "dark-theme");
    document.body.classList.add(localTheme);
}

themeToggle.addEventListener("click", () => {
    const themeUndefined = !new RegExp("(dark|light)-theme").test(document.body.className);
    const isOSDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

    if (themeUndefined) {
        if (isOSDark) {
            document.body.classList.add("light-theme");
        } else {
            document.body.classList.add("dark-theme");
        }
    } else {
        document.body.classList.toggle("light-theme");
        document.body.classList.toggle("dark-theme");
    }

    window.localStorage &&
        window.localStorage.setItem(
            "theme",
            document.body.classList.contains("dark-theme") ? "dark-theme" : "light-theme",
        );
});
// Darkmode End

//灯箱
document.addEventListener('click', function(e){
    if(e.target.closest('.img-popup img')){
        var src = e.target.closest('a').href;
        document.getElementById('img-lightbox').style.display = 'flex';
        document.querySelector('#img-lightbox img').src = src;
        e.preventDefault();
    } else if(e.target.id == 'img-lightbox' || e.target.closest('#img-lightbox')){
        document.getElementById('img-lightbox').style.display = 'none';
    }
});

//加载更多
document.addEventListener('click', function (e) {
    // 检查点击的元素是否是 .loadmore a
    if (e.target.closest('.loadmore a')) {
        e.preventDefault();
        var btn = e.target.closest('.loadmore a');
        var nextPage = btn.getAttribute('href');      
        // 防止重复点击
        if (btn.classList.contains('loading')) return false;
        btn.classList.add('loading');
        btn.textContent = '加载中...';      
        // 发起 AJAX 请求
        fetch(nextPage)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                // 创建一个临时的 DOM 元素来解析返回的 HTML
                var parser = new DOMParser();
                var htmlDoc = parser.parseFromString(data, 'text/html');            
                // 调试代码：检查选择器
                console.log('Searching for #memos:', htmlDoc.querySelectorAll('#memos'));
                console.log('Searching for .nav-links:', htmlDoc.querySelector('.nav-links'));              
                // 找到新的文章和按钮
                var newPosts = htmlDoc.querySelectorAll('#memos');
                var newBtn = htmlDoc.querySelector('.nav-links a');            
                // 更健壮的元素选择
                var articleList = document.querySelector('#memos') || 
                                  document.querySelector('.posts-container') || 
                                  document.body;
                var postReadMore = document.querySelector('.nav-links');               
                if (newPosts.length > 0) {
                    newPosts.forEach(post => {
                        // 使用 appendChild 替代 insertBefore
                        articleList.appendChild(post);
                    });                    
                    // 新文章淡入效果
                    Array.from(newPosts).forEach(post => {
                        post.style.opacity = 0;
                        setTimeout(() => {
                            post.style.transition = 'opacity 0.5s';
                            post.style.opacity = 1;
                        }, 10);
                    });
                }               
                // 更新"加载更多"按钮或移除它
                if (newBtn) {
                    btn.setAttribute('href', newBtn.getAttribute('href'));
                    btn.classList.remove('loading');
                    btn.textContent = '加载更多';
                } else {
                    // 如果没有更多的按钮，移除 .post-read-more
                    if (postReadMore) {
                        postReadMore.remove();
                    }
                }
            })
            .catch(error => {
                console.error("AJAX Error:", error);
                btn.classList.remove('loading');
                btn.textContent = '加载失败，点击重试';
            });
    }
});