import {languagesList} from './modules/languagesList';
import {headerMenu} from './modules/headerMenu';
import {heroCategoryList} from './mainPage/heroCategoryList';

//async loading for not important JS files
document.addEventListener("DOMContentLoaded", () => {

    //UI modules
    languagesList();
    headerMenu();
    heroCategoryList();

});