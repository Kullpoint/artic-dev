const heroCategoryList = () => {

    const categoryBtn = document.querySelector('[data-category-btn]');
    const categoryList = document.querySelector('[data-category-list]');
    const categoryInput = document.querySelector('[data-category-input]');
    const categoryItems = document.querySelectorAll('[data-category-item]');

    //category btn click logic
    categoryBtn.addEventListener('click', () => {
        categoryBtn.classList.toggle('active');
        //category list open logic
        if (categoryList.classList.contains('dn')) {
            categoryList.classList.remove('dn');
            setTimeout(() => {
                categoryList.classList.add('opened')
            }, 1);
        } else {
            //category list close logic
            categoryList.classList.remove('opened');
            setTimeout(() => {
                categoryList.classList.add('dn')
            }, 300);
        }
    });

    //category value change on click on an list item logic
    for (const categoryItem of categoryItems) {
        categoryItem.addEventListener('click', () => {
            const categoryValue = categoryItem.querySelector('[data-category-value]').innerHTML;
            categoryInput.innerHTML = categoryValue;
        });
    }

}

export {heroCategoryList};