const languagesList = () => {

    const languageBtn = document.querySelector('[data-languages-btn]');
    const languageList = document.querySelector('[data-language-list]');

    //language btn on click open list logic
    languageBtn.addEventListener('click', () => {
        languageBtn.classList.toggle('active');

        //open list logic
        if (languageList.classList.contains('dn')) {
            languageList.classList.remove('dn');
            setTimeout(() => {
                languageList.classList.add('opened');
            }, 1);
        } else {
            //close list logic
            languageList.classList.remove('opened');
            setTimeout(() => {
                languageList.classList.add('dn');
            }, 300);
        }
    });

}

export {languagesList};