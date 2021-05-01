import { Button, Grid } from "@material-ui/core";
import React from "react";
import { IoIosAdd, IoIosRemove } from 'react-icons/io';

const ProductOptions = ({bookDetails, add, deleteRow}) => {
    return bookDetails.map((val, idx) => {
        let name = `name-${idx}`,
        stock = `stock-${idx}`,
        price = `price-${idx}`;
        return (
        <div className="form-row" key={val.index}>
            <Grid item xs={12} md={3}>
            <label>Option Name</label>
            <input
                type="text"
                className="form-control required"
                placeholder="Name"
                name="name"
                data-id={idx}
                id={name}
            />
            </Grid>
            <Grid item xs={12} md={3}>
            <label>Option Stock</label>
            <input
                type="text"
                className="form-control"
                placeholder="Stock"
                name="stock"
                id={stock}
                data-id={idx}
            />
            </Grid>
            <Grid item xs={12} md={3}>
            <label>Option Price</label>
            <input
                type="text"
                className="form-control"
                placeholder="Price"
                name="price"
                id={price}
                data-id={idx}
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
                color="danger"
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
