

const MyModal = ({ data, onClose, onSave, onDataChange, categories }) => {


    const modalBackdropStyle = {
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        zIndex: 9999,
    };
    const rowContainerStyle = {
        display: 'flex', // Set the container to use flexbox
        gap: '1rem', // Adjust the gap between items as needed
        alignItems: 'center', // Vertically center the items
    };
    const modalContentStyle = {
        backgroundColor: '#fff',
        borderRadius: '8px',
        padding: '20px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.2)',
        width: '70%',
        maxWidth: '80%',
        maxHeight: '80%',
        overflow: 'auto',
    };

    const closeButtonStyle = {
        marginTop: '10px',
        cursor: 'pointer',
        backgroundColor: '#ccc',
        border: 'none',
        padding: '0.5rem 1rem',
        borderRadius: '4px',
    };
    const modalFormStyle = {
        display: 'grid',
        gap: '1rem',
    };

    const labelStyle = {
        display: 'block',
        fontWeight: 'bold',
        marginBottom: "-8px",
        textAlign: "start"
    };

    const inputStyle = {
        width: '100%',
        padding: '0.5rem',
        fontSize: '16px',
        fontWeight: 'normal',
        border: '1px solid #ccc',
        borderRadius: '4px',
    };

    const selectStyle = {
        width: '100%',
        padding: '0.5rem',
        fontSize: '16px',
        border: '1px solid #ccc',
        borderRadius: '4px',
    };

    const saveButtonStyle = {
        marginTop: '10px',
        marginLeft: '3px',
        cursor: 'pointer',
        border: 'none',
        padding: '0.5rem 1rem',
        borderRadius: '4px',
    };


    return (
        <div style={modalBackdropStyle}>
            <div style={modalContentStyle}>
                {/* Display the data in the modal */}
                <h2>{data.name}</h2>
                <form style={modalFormStyle}>

                <label for="name" style={labelStyle}>Name: </label>
                <input
                    name="name"
                    style={inputStyle}
                    type="text"
                    value={data.name}
                    onChange={(e) => onDataChange({ ...data, name: e.target.value })}

                />
                <label htmlFor="category" style={labelStyle}>Category: </label>
                <select name="category"
                        value={data.category_id}
                        style={selectStyle}
                        onChange={(e) => onDataChange({ ...data, category_id: e.target.value })}>
                    <option value="">Select a category</option>
                    {categories.map((category) => (
                        <option key={category.id} value={category.id}>
                            {category.name}
                        </option>
                    ))}
                </select>
                    <label htmlFor="address" style={labelStyle}>
                        Address:
                        <input name="address" style={inputStyle} type="text" value={data.address} onChange={(e) => onDataChange({ ...data, address: e.target.value })} />
                    </label>

                    <div style={rowContainerStyle}>
                        <label htmlFor="city" style={labelStyle}>
                            City:
                            <input name="city" style={inputStyle} type="text" value={data.city} onChange={(e) => onDataChange({ ...data, city: e.target.value })} />
                        </label>

                        <label htmlFor="state" style={labelStyle}>
                            State:
                            <input name="state" style={inputStyle} type="text" value={data.state} onChange={(e) => onDataChange({ ...data, state: e.target.value })} />
                        </label>
                    </div>

                    <label htmlFor="zip" style={labelStyle}>
                        Zip Code:
                        <input name="zip" style={inputStyle} type="text" value={data.zip_code} onChange={(e) => onDataChange({ ...data, zip_code: e.target.value })} />
                    </label>

                    <label htmlFor="phone" style={labelStyle}>
                        Phone:
                        <input name="phone" style={inputStyle} type="text" value={data.phone} onChange={(e) => onDataChange({ ...data, phone: e.target.value })} />
                    </label>

                    <label htmlFor="url" style={labelStyle}>
                        URL:
                        <input name="url" style={inputStyle} type="text" value={data.url} onChange={(e) => onDataChange({ ...data, url: e.target.value })} />
                    </label>

                    <label htmlFor="geo_code" style={labelStyle}>
                        Geo Code:
                        <input name="geo_code" style={inputStyle} type="text" value={data.geo_code} onChange={(e) => onDataChange({ ...data, geo_code: e.target.value })} />
                    </label>
                </form>


                {/* Close button */}
                <button style={closeButtonStyle} className={"button button-secondary"}  onClick={onClose}>Close</button>
                <button style={saveButtonStyle} className={"button button-primary"}  onClick={onSave}>Save</button>
            </div>
        </div>
    );
};

export default MyModal;