const handleSize = () => {
  const width = window.innerWidth;
  const height = window.innerHeight;
  if (width < height) {
    // alert("developer malas ngurus reponsive");
  } else {
    console.log("ok");
  }
};
document.addEventListener("resize", () => {
  handleSize();
});
document.addEventListener("DOMContentLoaded", () => {
  handleSize();
});
const getCookie = (name) => {
  const cookies = document.cookie.split(";");
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i].trim();
    if (cookie.startsWith(name + "=")) {
      return cookie.substring((name + "=").length, cookie.length);
    }
  }
  return null;
};
