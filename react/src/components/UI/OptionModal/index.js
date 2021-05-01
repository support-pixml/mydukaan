import { Backdrop, Button, ButtonGroup, Fade, Grid, makeStyles, Modal, Typography } from "@material-ui/core";
import React, { useState } from "react";
import { useDispatch } from "react-redux";
import { addProductOption, increaseOption } from "../../../actions/cart";

const useStyles = makeStyles((theme) => ({
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
        width: '500px'
    },
}));

const OptionModal = ({product_options, handleCloseModal, openOption, cartItems}) => {
    const classes = useStyles();
    const [counter, setCounter] = useState(0);
    const dispatch = useDispatch();
    const displayCounter = counter > 0;  

    return (
        <Grid item xs={12} md={4} key={index} className="text-center">
            <Typography component="p" variant="body1">
                {option.option_name}
            </Typography>
            <Typography component="p" variant="body2">
                Stock: {option.option_stock}
            </Typography>
            <Typography component="p" variant="caption">
                &#8377; {option.option_price}
            </Typography>
            <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
                {displayCounter && <Button onClick={handleDecrement}>-</Button>}
                {displayCounter && <Button disabled>{option.add_quantity}</Button>}
                <Button onClick={() => handleIncrement(option)}>+</Button>
            </ButtonGroup>
        </Grid>   
    )
    };

export default OptionModal;