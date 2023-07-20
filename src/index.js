//const { render } = wp.element; //we are using wp.element here!
import { createRoot, render, createElement } from '@wordpress/element';

import App from './App';



document.addEventListener("DOMContentLoaded",()=>{
    if (document.getElementById('poi-root')) { //check if element exists before rendering
        render(<App />, document.getElementById('poi-root'));
    }
})