import { TextField } from '@material-ui/core';
import React from 'react';

const Input = ({variant, margin, label, type, placeholder, errorMessage, value, name, handleChange, multiline, rows, required, fullWidth, id, autoFocus}) => {
    
    return (
        <TextField
            id={id}
            label={label}
            margin={margin}
            type={type}
            value={value}
            name={name}
            helperText={errorMessage}
            placeholder={placeholder}
            onChange={handleChange}
            variant={variant}
            required={required}
            fullWidth={fullWidth}
            autoFocus={autoFocus}
            multiline={multiline}
            rows={rows}
        />
    )
}

export default Input;