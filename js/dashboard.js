const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

hamburger.onclick = () => {
  sidebar.classList.toggle("show");
};

function updateTime() {
  const now = new Date();
  document.getElementById("date").innerText = now.toDateString();
  document.getElementById("time").innerText = now.toLocaleTimeString();
}
setInterval(updateTime, 1000);
updateTime();
