import { useState,useEffect } from '@wordpress/element';

import "./InformationModal.css"; // Import your CSS styles for the modal
const InformationModal = ({ messageType, message, showInfo, seconds = 3 }) => {
    const [visible, setVisible] = useState(true);

    useEffect(() => {
        // Set a timeout to hide the modal after 3 seconds
        const timer = setTimeout(() => {
            setVisible(false);
            showInfo(false);
        }, seconds * 1000);

        // Clear the timeout when the component unmounts
        return () => clearTimeout(timer);
    }, []);

    const closeModal = () => {
        showInfo(false);
        setVisible(false);
    };

    if (!visible) {
        return null; // If not visible, return null to hide the modal
    }

    return (
        <>
        {messageType === 'success' &&
            <div className={`information-modal ${messageType}` + "bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"} role={'alert'}>
                <strong className="font-bold">{messageType}</strong>

                <div className="modal-content">
                    <span>{message}</span>
                    <button className="close-button" onClick={closeModal}>
                        &#x2715;
                    </button>
                </div>
            </div>
        }
            {messageType === 'info' &&
                <div
                    className={`information-modal ${messageType}` + "bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative"}
                    role={'alert'}>
                    <strong className="font-bold">{messageType}</strong>

                    <div className="modal-content">
                        <span>{message}</span>
                        <button className="close-button" onClick={closeModal}>
                            &#x2715;
                        </button>
                    </div>
                </div>
            }
            {messageType === 'danger' &&
                <div className={`information-modal ${messageType}` + "bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"} role={'alert'}>
                    <strong className="font-bold">{messageType}</strong>

                    <div className="modal-content">
                        <span>{message}</span>
                        <button className="close-button" onClick={closeModal}>
                            &#x2715;
                        </button>
                    </div>
                </div>
            }
            {messageType === 'warning' &&
                <div className={`information-modal ${messageType}` + "bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative"} role={'alert'}>
                    <strong className="font-bold">{messageType}</strong>

                    <div className="modal-content">
                        <span>{message}</span>
                        <button className="close-button" onClick={closeModal}>
                            &#x2715;
                        </button>
                    </div>
                </div>
            }
        </>

    );
};

export default InformationModal;