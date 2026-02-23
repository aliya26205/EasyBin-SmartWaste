function updateDateTime() {
  const now = new Date();
  console.log("heelo");
  document.getElementById("date").innerText =
    now.toLocaleDateString(undefined, {
      weekday: "short",
      year: "numeric",
      month: "short",
      day: "numeric"
    });

  document.getElementById("time").innerText =
    now.toLocaleTimeString();
}

setInterval(updateDateTime, 1000);
updateDateTime();
