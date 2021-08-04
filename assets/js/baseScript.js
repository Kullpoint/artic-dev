import 'idempotent-babel-polyfill';
import LazyLoad from "vanilla-lazyload";

//including lazy loading
const lazyLoadInstance = new LazyLoad({
  // your custom settings go here
});
lazyLoadInstance.update();

//async loading for not important JS files
document.addEventListener("DOMContentLoaded", () => {
  
  //UI modules

});