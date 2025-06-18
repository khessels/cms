<script>

    let editBtn = document.getElementById('editBtn');
    let editables = document.querySelectorAll('.editable')

    editBtn.addEventListener('click', function(e) {
        if(typeof editables[0].isContentEditable !== 'undefined') {
            if (!editables[0].isContentEditable) {
                for (let x = 0; x < editables.length; x++) {
                    editables[x].contentEditable = 'true';
                }
                editBtn.innerHTML = 'Save Changes';
                editBtn.style.backgroundColor = '#6F9';
            } else {

                // Disable Editing
                for (let x = 0; x < editables.length; x++) {
                    editables[x].contentEditable = 'false';
                }
                // Change Button Text and Color
                editBtn.innerHTML = 'Enable Editing';
                editBtn.style.backgroundColor = '#F96';

                // save data to db
                let newContent = [];
                for (let i = 0; i < editables.length; i++) {
                    let needle = editables[i].getAttribute('data-needle')
                    let page  = null;
                    if (typeof editables[i].getAttribute('data-page') !== 'undefined'){
                        page = editables[i].getAttribute('data-page');
                    }

                    let locale = '{{ App::getLocale() }}';
                    if(typeof editables[i].getAttribute('data-locale') !== 'undefined' && editables[i].getAttribute('data-locale') !== null ){
                        locale = editables[i].getAttribute('data-locale');
                    }
                    let content = editables[i].textContent;
                    let o = {
                        'needle'    : needle,
                        'locale'    : locale,
                        'page'      : page,
                        'content'   : btoa( content)
                    }
                    newContent.push( o);
                    //localStorage.setItem(editables[i].getAttribute('id'), editables[i].innerHTML);
                }
                window.axios.post('/content', newContent)
                    .then(response => {
                        console.log( response.data);
                    })
                    .catch(error => {
                        console.log(error.response);
                    });
            }
        }
    });
</script>
