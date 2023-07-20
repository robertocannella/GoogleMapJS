const { render } = wp.element; //we are using wp.element here!

import App from './App';

console.log("loaded react app")

document.addEventListener("DOMContentLoaded",()=>{
    if (document.getElementById('poi-root')) { //check if element exists before rendering
        render(<App />, document.getElementById('poi-root'));
    }
})