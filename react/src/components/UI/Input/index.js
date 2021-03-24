import React from 'react';
import { Form } from 'react-bootstrap';

const Input = ({label, type, placeholder, errorMessage, value, name, handleChange}) => {
    return (
        <Form.Group controlId={name}>
            <Form.Label>{label}</Form.Label>
            <Form.Control name={name} type={type} placeholder={placeholder} value={value} onChange={handleChange} />
            <Form.Text className="text-muted">
            {errorMessage}
            </Form.Text>
        </Form.Group>
    )
}

export default Input;