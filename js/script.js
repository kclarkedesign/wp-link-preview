const linkPreview = (url) => {
  let html = "";
  fetch(url, { mode: "no-cors" })
    .then((response) => response.text())
    .then((text) => {
      console.log(text);
    });
};
