$(document).ready(function () {
    // const sideMenu = document.getElementById("sidebar");
    // const menuBtn = document.getElementById("menu-btn");
    // const closeBtn = document.getElementById("close-btn");

    // menuBtn.addEventListener("click", () => {
    //     sideMenu.style.display = "block";
    // });

    // closeBtn.addEventListener("click", () => {
    //     sideMenu.style.display = "none";
    // });

    $('html').click(function () {
        $('#sidebar').removeClass("active");
    });

    $('#menu-btn').click(function (e) {
        e.stopPropagation();
        $('#sidebar').toggleClass("active");
    });

    $('#close-btn').click(function (e) {
        e.stopPropagation();
        $('#sidebar').removeClass("active");
    });

    $('#sidebar').click(function (e) {
        e.stopPropagation();
    });
});