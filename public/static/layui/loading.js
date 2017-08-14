var div = document.createElement("div");
div.id = "loading";
div.style.zIndex = 999;
div.style.position = "fixed";
div.style.top = 0;
div.style.right = 0;
div.style.bottom = 0;
div.style.left = 0;
div.style.background = "#fff";
div.style.textAlign = "center";
div.style.color = "#3597e5";
div.style.paddingTop = "100px";
div.innerHTML = '正在加载...';
document.body.appendChild(div);
window.onload = loading;
function loading() {
    setTimeout(function () {
        document.getElementById('loading').remove();
    }, 100);
}

