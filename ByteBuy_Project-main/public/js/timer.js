document.addEventListener("DOMContentLoaded", () => {
  const timers = document.querySelectorAll(".timer");

  timers.forEach(timer => {
    const now = new Date();
    const end = new Date();
    end.setHours(23, 59, 59, 999); // end of day

    function updateTimer() {
      const remaining = end - new Date();

      if (remaining <= 0) {
        timer.innerHTML = "<span>EXPIRED</span>";
        return;
      }

      const hours = Math.floor((remaining / (1000 * 60 * 60)) % 24);
      const minutes = Math.floor((remaining / (1000 * 60)) % 60);
      const seconds = Math.floor((remaining / 1000) % 60);

      timer.innerHTML = `
        <div><span class="value">${String(hours).padStart(2, "0")}</span><span class="label">HRS</span></div>
        <div><span class="value">${String(minutes).padStart(2, "0")}</span><span class="label">MIN</span></div>
        <div><span class="value">${String(seconds).padStart(2, "0")}</span><span class="label">SEC</span></div>
      `;
    }

    updateTimer();
    setInterval(updateTimer, 1000);
  });
});
