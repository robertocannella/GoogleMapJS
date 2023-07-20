import { useState,useEffect } from '@wordpress/element';
import ReviewData from "./ReviewData";
import {forwardAsync} from "@babel/core/lib/gensync-utils/async";



const App = () => {


    const [file, setFile] = useState();     // Submitted File
    const [array, setArray] = useState([]); // Parsed CSV
    const [asc, setAsc] = useState(true);   // Table Sorter
    const [imported, setImported] = useState(false) // Track session
    const [review, setReview] = useState(false);    // Track Session
    const fileReader = new FileReader();




    const handleOnChange = (e) => {
        setFile(e.target.files[0]);
    };


    const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

    const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

    const handleSort = (event) => {
        const th = event.currentTarget;
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');


        setAsc((asc) => !asc);

       const sortedArray = Array.from(tbody.querySelectorAll('tr'))
            .sort(comparer(Array.from(th.parentNode.children).indexOf(th), asc))


        tbody.innerHTML = ''; // Remove all rows

        sortedArray.forEach((tr) => tbody.appendChild(tr)); // Append sorted rows back
    };

    const csvFileToArray = string => {

        const csvHeader = string.slice(0, string.indexOf("\n")).split("\t");
        const csvRows = string.slice(string.indexOf("\n") + 1).split("\n");

        const array = csvRows.map(i => {
            const values = i.split("\t");
            const obj = csvHeader.reduce((object, header, index) => {
                object[header] = values[index];
                return object;
            }, {});
            return obj;
        });

        setArray(array);
    };
    const handleOnImport = (e) => {
        e.preventDefault();


        console.log(e)
    }
    const handleOnSubmit = (e) => {
        e.preventDefault();
        if (!file){
            alert("Please choose a file.")
            return;
        }
        setImported(true);

        if (file) {
            fileReader.onload = function (event) {
                const text = event.target.result;
                csvFileToArray(text);
            };

            fileReader.readAsText(file);
        }

    };

    const headerKeys = Object.keys(Object.assign({}, ...array));

    return (
        <div style={{ textAlign: "center", overflow: "auto" }}>
            <h2> Import New Points of Interest Data </h2>
            <p>File must be a tab separated file. For example, 'points_of_interest.txt.'  If you are using excel, select all the cells, copy and paste the content into a txt file.</p>

            <form>
                <input

                    type={"file"}
                    id={"csvFileInput"}
                    accept={".txt"}
                    onChange={handleOnChange}
                />

                <button
                    className={"button button-primary"}
                    onClick={(e) => {
                        handleOnSubmit(e);
                    }}
                >
                    Review TSV
                </button>
            </form>

            <br />

            {imported &&

                <div style={{overflowY: "auto", height: '600px'}}>
                    <table className={"widefat"} cellSpacing={0}>
                        <thead>
                        <tr key={"header"}
                            style={{textAlign: "initial", whiteSpace: 'nowrap', cursor: 'pointer'}}
                            className={"manage-column column-columnname"}
                            scope={"col"}
                        >
                            {headerKeys.map((key) => (
                                <th style={{position: 'sticky', top: '0', backgroundColor: "white"}}
                                    onClick={handleSort}>{key}</th>
                            ))}
                        </tr>
                        </thead>

                        <tbody>
                        {array.map((item) => (
                            <tr key={item.id}
                                style={{textAlign: "initial", whiteSpace: 'nowrap'}}
                                className={"alternate"}
                            >

                                {Object.values(item).map((val) => (
                                    <td>{val}</td>
                                ))}
                            </tr>
                        ))}
                        </tbody>
                    </table>

                </div>
            }
            { imported &&
                <div>
                    <p>Look good?</p>
                    <button
                        className={"button button-primary"}
                        onClick={(e) => {
                            handleOnImport(e);
                        }}
                    >
                        Import Data
                    </button>
                    <button
                        className={"button button-secondary"}
                        onClick={(e) => {
                            setImported(false);
                        }}
                    >
                       Cancel
                    </button>
                </div>

            }
            {!imported &&
                <>
                    <hr/>
                    <h2> Review or Edit current POI data </h2>
                    {!review &&
                        <button
                            className={"button button-primary"}
                            onClick={(e) => {
                                setReview(true);
                                setImported(false);

                            }}
                        >
                            Load
                        </button>
                    }
                    {review && <ReviewData/> }

                </>

            }

        </div>
    );
}
export default App;
