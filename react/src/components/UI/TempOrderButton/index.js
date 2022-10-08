import React, { useEffect, useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { useDispatch } from "react-redux";
import { makeStyles } from "@material-ui/core";

const useStyles = makeStyles((theme) => ({
    qtyBtn: {
        color: '#000000 !important',
        fontWeight: '700'
    },
}));

const TempOrderButton = ({order, detail, quantity, setEditOrder}) => {
    const [counter, setCounter] = useState(parseInt(quantity));
    const [disabled, setDisabled] = useState(false);
    const dispatch = useDispatch();
    const classes = useStyles();

    useEffect(() => {
        setEditOrder();
        if(counter == 0)
        {   
            setDisabled(true);
        }
        else
        {
            setDisabled(false);
        }
        order.order_details.find(item => item.long_id === detail.long_id).quantity = counter;
        setEditOrder(order);
    }, [counter]);

    const handleIncrement = () => {  
        setCounter(counter+1);
    };

    const handleDecrement = () => {
        setCounter(counter-1);   
    };  

    if(new Date(customer.expiry_date).getTime() < new Date().getTime())
    {
        return false;
    }

    return (
        <ButtonGroup size="small" color="primary" aria-label="small outlined button group">
            <Button disabled={disabled} onClick={handleDecrement}>-</Button>                
            <Button disabled className={classes.qtyBtn}>{counter}</Button>
            <Button onClick={handleIncrement}>+</Button>
        </ButtonGroup>
    );
}

export default TempOrderButton;
