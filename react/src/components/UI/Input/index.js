import React from 'react';
import { TextValidator } from 'react-material-ui-form-validator';

const Input = ({variant, margin, label, type, validators, placeholder, errorMessages, value, name, handleChange, multiline, rows, required, fullWidth, id, autoFocus}) => {
    
    return (
        <TextValidator
            id={id}
            label={label}
            margin={margin}
            type={type}
            value={value}
            name={name}
            placeholder={placeholder}
            onChange={handleChange}
            variant={variant}
            required={required}
            fullWidth={fullWidth}
            autoFocus={autoFocus}
            multiline={multiline}
            rows={rows}
            validators={validators}
            errorMessages={errorMessages}
        />
    )
}

export default Input;