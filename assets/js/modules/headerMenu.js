const headerMenu = () => {

    const headerMenuBtn = document.querySelector('[data-header-menu-btn]');
    const headerNav = document.querySelector('[data-header-menu-nav]');
    const headerForm = document.querySelector('[data-header-menu-form]');

    //header menu btn click logic
    headerMenuBtn.addEventListener('click', () => {
        headerMenuBtn.classList.toggle('active');

        //show nav list and hide search form
        if (headerMenuBtn.classList.contains('active')) {
            //header nav
            headerForm.classList.remove('opened');
            setTimeout(() => {
                headerForm.classList.add('dn');
            }, 150);
            //header search
            setTimeout(() => {
                headerNav.classList.remove('dn');
            }, 150);
            setTimeout(() => {
                headerNav.classList.add('opened');
            }, 151);
        } else {
            //hide nav list and show search form
            //header nav
            headerNav.classList.remove('opened');
            setTimeout(() => {
                headerNav.classList.add('dn');
            }, 150);
            //header search
            setTimeout(() => {
                headerForm.classList.remove('dn');
            }, 150);
            setTimeout(() => {
                headerForm.classList.add('opened');
            }, 151);
        }
    });

}

export {headerMenu};