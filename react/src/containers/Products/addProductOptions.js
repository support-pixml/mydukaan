import { Button, Grid } from "@material-ui/core";
import React from "react";
import { IoIosAdd, IoIosRemove } from 'react-icons/io';

const ProductOptions = ({bookDetails, add, deleteRow}) => {
    return bookDetails.map((val, idx) => {
        let name = `option-name-${idx}`,
        stock = `option-stock-${idx}`,
        price = `option-price-${idx}`;
        return (
        <div className="form-row" key={idx}>
            <Grid item xs={12} md={3}>
            <label>Option Name</label>
            <input
                type="text"
                className="form-control required"
                placeholder="Name"
                name="option_name"
                data-id={idx}
                id={name}
                defaultValue={val.option_name}
            />
            </Grid>
            <Grid item xs={12} md={3}>
            <label>Option Stock</label>
            <input
                type="text"
                className="form-control"
                placeholder="Stock"
                name="option_stock"
                id={stock}
                data-id={idx}
                defaultValue=""
            />
            </Grid>
            <Grid item xs={12} md={3}>
            <label>Option Price</label>
            <input
                type="text"
                className="form-control"
                placeholder="Price"
                name="option_price"
                id={price}
                data-id={idx}
                defaultValue={val.option_price}
            />
            </Grid>
            <Grid item xs={12} md={3}>
            {idx === 0 ? (
                <Button
                onClick={() => add()}
                color="primary"
                variant="outlined"
                className="text-center"
                >
                <IoIosAdd />
                </Button>
            ) : (
                <Button
                color="secondary"
                variant="outlined"
                className="btn btn-danger"
                onClick={() => deleteRow(val)}
                >
                <IoIosRemove />
                </Button>
            )}
            </Grid>
        </div>
        );
    });
};
export default ProductOptions;
