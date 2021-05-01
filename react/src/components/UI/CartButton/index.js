import React, { useState } from "react";
import Button from "@material-ui/core/Button";
import ButtonGroup from "@material-ui/core/ButtonGroup";
import { addProduct, decrease, increase, removeProduct, addOption, increaseOption } from "../../../actions/cart";
import { useDispatch } from "react-redux";
import { Backdrop, Fade, Grid, makeStyles, Modal, Typography } from "@material-ui/core";
import OptionModal from "../OptionModal";

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

const CartButton = ({product, cartItems}) => {
    const classes = useStyles();
    const [counter, setCounter] = useState(0);
    const [openOption, setOpenOption] = useState(false);
    const dispatch = useDispatch();

    const isInCart = (product) => {
        return !!cartItems.find(item => item.long_id === product.long_id);
    }

    const isInProduct = (opt) => {
        cartItems.forEach(function(e) {

            console.log('options', e.product_options);

            (e.product_options.filter(function(c) {

                 Promise.resolve(c.product_id === opt.product_id).then(function(result) {
                    return result;
                });

                }));
        });
    }
    
    const handleCloseModal = () => {
        setOpenOption(false);
    };

    const handleOpenModal = () => {
        setOpenOption(true);
    };

    const handleOptionIncrement = (opt) => {
        let addedProd = Object.assign({}, product);
        console.log('option',opt);
        if(isInCart(addedProd))
        {
            cartItems.forEach(function(e) {

            (e.product_options.filter(function(c) {

                 if(c.product_id === opt.product_id) {
                    dispatch(increaseOption(opt));
                 } else {
                    dispatch(addOption(opt));
                 }

                }));
        });

            // if (isInProduct(opt)) {
            //     console.log('inc option');
                
            // } else {
            //     console.log('add option');
                
            // }
            // console.log('1');
        }
        else
        {
            console.log('2');
            if (opt !== null) {
                addedProd.product_options = [];
                addedProd.product_options.push(opt);
                console.log(addedProd);
            };
            dispatch(addProduct(addedProd));
        }
        setCounter(counter+1);
    }

    const handleIncrement = () => {
        let addedProd = Object.assign({}, product);

        if(isInCart(addedProd))
        {
            console.log('1');
            dispatch(increase(addedProd));
        }
        else
        {
            console.log('2');
            dispatch(addProduct(addedProd));
        }
        setCounter(counter+1);
    };

    const handleDecrement = () => {
        if(isInCart(product))
        {
            dispatch(decrease(product));
            setCounter(counter-1);
            console.log('Counter');
            console.log(counter);

            
            if(counter == 1)
            {
                console.log('yes');
                dispatch(removeProduct(product));
            }
        }
    };
    const displayCounter = counter > 0;    

    // const renderOptionModal = (product) => {
    //     return (
    //     <Modal
    //         className={classes.modal}
    //         open={openOption}
    //         onClose={handleCloseModal}
    //         closeAfterTransition
    //         BackdropComponent={Backdrop}
    //         BackdropProps={{
    //             timeout: 500,
    //         }}
    //     >
    //         <Fade in={openOption}>
    //             <div className={classes.paper}>
    //                 <Typography component="h1" variant="h5">
    //                     Select Option
    //                 </Typography>
    //                 <Grid container spacing={2}>                        
    //                         {product.product_options.map((option, index) => {
    //                             return (
    //                                 <Grid item xs={12} md={4} key={index} className="text-center">
    //                                     <Typography component="p" variant="body1">
    //                                         {option.option_name}
    //                                     </Typography>
    //                                     <Typography component="p" variant="body2">
    //                                         Stock: {option.option_stock}
    //                                     </Typography>
    //                                     <Typography component="p" variant="caption">
    //                                         &#8377; {option.option_price}
    //                                     </Typography>
    //                                     <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
    //                                         <Button onClick={() => handleOptionIncrement(option)}>+</Button>
    //                                         {displayCounter && <Button disabled>{option.add_quantity}</Button>}
    //                                         {displayCounter && <Button onClick={handleDecrement}>-</Button>}
    //                                     </ButtonGroup>
    //                                 </Grid>   
    //                             )
    //                         })}
    //                 </Grid>
    //             </div>
    //         </Fade>
    //     </Modal>
    //     );
    // }

    if(product.stock > 0 && product.product_options.length == 0)
    {
        return (
            <ButtonGroup size="small" color="secondary" aria-label="small outlined button group" className="float-right">
                {displayCounter && <Button onClick={handleDecrement}>-</Button>}
                {displayCounter && <Button disabled>{counter}</Button>}
                <Button onClick={handleIncrement}>+</Button>
            </ButtonGroup>
        );
    }
    else if (product.stock == 0 && product.product_options.length > 0)
    {
        return (
            <>
            <div className="float-right">
                <Button size="small" color="secondary" variant="outlined" onClick={handleOpenModal}>Select</Button>
            </div>
            <Modal
                className={classes.modal}
                open={openOption}
                onClose={handleCloseModal}
                closeAfterTransition
                BackdropComponent={Backdrop}
                BackdropProps={{
                    timeout: 500,
                }}
            >
                <Fade in={openOption}>
                    <div className={classes.paper}>
                        <Typography component="h1" variant="h5">
                            Select Option
                        </Typography>
                        <Grid container spacing={2}>      
                            {product.product_options.map((option, index) => {
                                <OptionModal option={option} />
                            })}
                        </Grid>
                    </div>
                </Fade>
            </Modal>
            </>
        )
    }
    else
    {
        return (
            <div>No Stock</div>
        )
    }
}

export default CartButton;
