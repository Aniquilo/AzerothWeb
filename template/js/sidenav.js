function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
 }
 

const $resForm = document.querySelector('.unsimple');
const copy = `
<a class="unsimple" href="https://unsimpleworld.com/" target="_blank" rel="dofollow" title="Developed by Unsimple World"><img src="assets/img/unsimple.png"/><span>Website Developed by<br>Unsimple World</span></a>
`;

$resForm || document.querySelector('.footer-end2img').insertAdjacentHTML('beforeend', copy);