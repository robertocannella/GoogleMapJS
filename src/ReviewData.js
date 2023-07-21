import { useState,useEffect } from '@wordpress/element';
import poiService from "./services/poi-service";
import catService from "./services/category-service";
import EditModal from "./EditModal";
import InformationModal from "./InformationModal";

export default function ReviewData  () {
    const [loading, setLoading] = useState(false);
    const [data, setData] = useState([]);
    const [asc, setAsc] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalData, setModalData] = useState({});
    const [categories, setCategories] = useState([]);
    const [showInformation, setShowInformation] = useState(false)
    const [information, setInformation] = useState({messageType: '', message: ''});

    useEffect(() => {
        setLoading(true);
        const {request: catRequest, cancel: catCancel } = catService.getAll();
        const {request, cancel} = poiService.getAll();



        request.then(res=>{
            setData(res.data.data.poiData)
            setLoading(false);

            catRequest.then(res=>{
                setCategories(res.data.poiCategories)
                setLoading(false);
            })

        })

    }, []);
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


    // Function to open the modal with data
    const openModalWithData = (data) => {
        setModalData(data);
        setIsModalOpen(true);
    };

    // Function to close the modal
    const closeModal = () => {
        setIsModalOpen(false);
    };
    const saveData = () => {
        setIsModalOpen(false);

        const elementIndex = data.findIndex((item) => item.id === modalData.id);

        // If the element exists, update its data; otherwise, add it to the array
        // This preserves the order of the table.
        if (elementIndex !== -1) {
            const updatedMainData = [...data];
            updatedMainData[elementIndex] = modalData;
            setData(updatedMainData);
        } else {
            setData((prevData) => [...prevData, modalData]);// ADDs NEw data
        }
        // Here make api request to save data to DB.
        poiService.createOrUpdate(modalData).then(res=> {
            console.log(res)
            setInformation({messageType: "success", message: "Data updated successfully."})
            setShowInformation(true)
        })


    }
    const handleModalDataChange = (newData) => {
        setModalData(newData);
    };
    const headerKeys = Object.keys(Object.assign({}, ...data));

    return (
        <>
            {loading && <div>Loading...</div>}
            <div style={{overflowY: "auto", height: '600px'}}>
                <table className={"widefat"} cellSpacing={0}>
                    <thead>
                    <tr key={"header"}
                        style={{textAlign: "initial", whiteSpace: 'nowrap', cursor: 'pointer'}}
                        className={"manage-column column-columnname"}
                        scope={"col"}
                    >
                        <th>Edit</th>
                        {headerKeys.map((key) => (

                            <th style={{position: 'sticky', top: '0', backgroundColor: "white"}}
                                onClick={handleSort}>{key === 'category_id' ? 'category' : key}</th>
                        ))}
                    </tr>
                    </thead>

                    <tbody>
                    {data.map((item) => (

                        <tr key={item.id}
                            style={{textAlign: "initial", whiteSpace: 'nowrap'}}
                            className={"alternate"}
                        >
                            <td onClick={()=>openModalWithData(item)} style={{position: "absolute", backgroundColor: "lavender"}}>
                                <i
                                    style={{cursor: "pointer"}}
                                    className="fa-regular fa-pen-to-square"></i>
                            </td>
                            {Object.keys(item).map((key) => (
                                <td key={key}>
                                    {/* If the key is "category_id", retrieve the corresponding name from categoriesMap */}
                                    {key === "category_id"
                                        ? categories.find((category) => category.id === item[key])?.name ||
                                        "Category Not Found" // Handle if the category is not found
                                        : item[key]}
                                </td>
                            ))}
                        </tr>
                    ))}
                    </tbody>
                </table>

            </div>
            {showInformation && <InformationModal seconds={5} showInfo={setShowInformation} message={information.message} messageType={information.messageType}/>}
            {isModalOpen && <EditModal categories={categories} data={modalData} onClose={closeModal} onDataChange={handleModalDataChange} onSave={saveData} />}
        </>
    );
}