import { useState,useEffect } from '@wordpress/element';
import ReviewData from "./ReviewData";
import poiNewDataService from "./services/poi-new-data-service";
import poiNewCategoriesService from "./services/poi-new-categories-service";

class Category {
    id;
    name;
    hex_color;
}

class POIItem {
    id;
    name;
    address;
    city;
    state;
    zip_code;
    phone;
    url;
    geo_code;
    category_id

}

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

        const csvHeader = string.slice(0, string.indexOf("\n")).split("\t").map((header) => header.trim());
        const csvRows = string.slice(string.indexOf("\n") + 1).split("\n");

        const array = csvRows.map(i => {
            const values = i.split("\t").map((value) => value.trim());
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

        let newCategories = [];
        let count = 0;
        let items = [];

        array.forEach((item)=> {
            const newItem = new POIItem();
            newItem.name = item.name;
            newItem.city = item.city;
            newItem.address = item.address;
            newItem.geo_code = item.geo_code;
            newItem.phone = item.phone;
            newItem.state = item.state;
            newItem.url = item.url;
            newItem.zip_code = item.zip_code;

            const existingCategory = newCategories.find((category) => category.name === item.category);

            if (existingCategory) {
                // If the category exists, assign its id to the poiItem

                newItem.category_id = existingCategory.id;

            } else {
                // If the category does not exist, create a new category object
                const newCategory = {
                    id: newCategories.length + 1, // Generate a new id (you can use any logic to generate unique ids)
                    name: item.category,
                    hex_color: 'ffffff'
                };

                // Push the new category object to the categories array
                newCategories.push(newCategory);

                // Assign the new category id to the poiItem
                newItem.category_id = newCategory.id;

            }

            items.push(newItem);


        })
        // ADD THE CATEGORIES FIRST
        poiNewCategoriesService.createOrUpdate(newCategories).then((res)=>{

            // THEN ADD THE DATA
            poiNewDataService.createOrUpdate(items).then((res)=>
            {
                setImported(false);

            })

        })





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
        <div style={{ overflow: "auto" }}>
            <div className={"border-2 p-4"}>
            <h2 className={"text-2xl pb-2"}> Import New Points of Interest Data </h2>
            <ul className={"list-disc"}>
                <li className={"list-item"}>
                    File must be a tab separated file. For example, 'points_of_interest.txt.'
                </li>
                <li className={"list-item"}>
                If you are using excel, select all the cells, copy and paste the content into a txt file.
                </li>
                <li className={"list-item"}>
                    Importing a new file will overwrite existing data, including CATEGORIES and COLOR MAP
                </li>
                <li className={"list-item"}>
                    After reviewing the data, click the import button below the table.
                </li>
            </ul>

            <form>
                <input

                    type={"file"}
                    id={"csvFileInput"}
                    accept={".txt"}
                    onChange={handleOnChange}
                />

                <button
                    className={"button button-primary "}
                    onClick={(e) => {
                        handleOnSubmit(e);
                    }}
                >
                    Review TSV
                </button>
            </form>
            </div>
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
                        className={"button button-secondary p-4"}
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
                    <h2  className={"text-2xl"}> Review or Edit current POI data </h2>
                    {!review &&
                        <button
                            className={"button button-primary p-4"}
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
