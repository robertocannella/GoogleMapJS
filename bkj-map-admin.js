
document.addEventListener('DOMContentLoaded',(event)=>{
    const secretFields = document.querySelectorAll('.key-field');
    const revealButton = document.querySelector('#reveal-secrets-button')
    console.log("dev plugin loaded")
    if (revealButton){
        revealButton.addEventListener('click',()=>{
            if (revealButton.innerHTML === 'Reveal'){
                secretFields.forEach(field=>{
                    field.setAttribute('type','text');

                })
                revealButton.innerHTML = 'Hide';
            }else {
                secretFields.forEach(field=>{
                    field.setAttribute('type','password');

                })
                revealButton.innerHTML = 'Reveal';
            }

        })
    }





})