const date = document.querySelector(".date");
const time = document.querySelector(".time");

function formatDate(date){
  const DAYS = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"];

  const MONTHS = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ];

  return `${DAYS[date.getDay()]}, ${MONTHS[date.getMonth()]} ${date.getDate()} ${date.getFullYear()}`;
}

function formatTime(date){
  const hours = date.getHours() % 12 || 12;
  const mins = date.getMinutes();
  const isAM = date.getHours() < 12;

  return `${hours.toString().padStart(2,"0")}:${mins.toString().padStart(2,"0")} ${isAM ? "AM" : "PM"}`;
}

setInterval(() => {
  const now = new Date();
  date.textContent = formatDate(now);
  time.textContent = formatTime(now);
}, 200);
