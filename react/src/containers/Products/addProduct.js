import { Button, Container, CssBaseline, FormControl, Grid, InputLabel, makeStyles, Paper, Select, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, TextareaAutosize, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { Link, Redirect } from 'react-router-dom';
import Input from '../../components/UI/Input';
import {useDispatch, useSelector} from 'react-redux';
import { getCategories } from '../../actions/categories';
import { addProduct } from '../../actions/products';

const initialState = {
    name: '', image: null, category_id: '', price: '', stock: '', description: ''
};

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(15),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
    formControl: {
        width: '100%', 
    },
    selectEmpty: {
        marginTop: theme.spacing(2),
    },
    table: {
        minWidth: 650,
    },
}));

const AddProduct = () => {
    const classes = useStyles();
    const [productData, setProductData] = useState(initialState);
    const dispatch = useDispatch();
    var formData = new FormData();

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    const categories = useSelector((state) => state.category.categories);

    const submitProduct = (e) => {
        e.preventDefault();
        formData.append('name', productData.name);
        formData.append('category_id', productData.category_id);
        formData.append('price', productData.price);
        formData.append('stock', productData.stock);
        formData.append('description', productData.description);
        formData.append('image', productData.image);
        dispatch(addProduct(formData));
        setProductData(initialState);
    }

    function createData(name, calories, fat, carbs, protein) {
    return { name, calories, fat, carbs, protein };
    }

    const rows = [
    createData('Frozen yoghurt', 159, 6.0, 24, 4.0),
    createData('Ice cream sandwich', 237, 9.0, 37, 4.3),
    createData('Eclair', 262, 16.0, 24, 6.0),
    createData('Cupcake', 305, 3.7, 67, 4.3),
    createData('Gingerbread', 356, 16.0, 49, 3.9),
    ];

    const renderProductsTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Dessert (100g serving)</TableCell>
                        <TableCell align="right">Calories</TableCell>
                        <TableCell align="right">Fat&nbsp;(g)</TableCell>
                        <TableCell align="right">Carbs&nbsp;(g)</TableCell>
                        <TableCell align="right">Protein&nbsp;(g)</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {rows.map((row) => (
                        <TableRow key={row.name}>
                        <TableCell component="th" scope="row">
                            {row.name}
                        </TableCell>
                        <TableCell align="right">{row.calories}</TableCell>
                        <TableCell align="right">{row.fat}</TableCell>
                        <TableCell align="right">{row.carbs}</TableCell>
                        <TableCell align="right">{row.protein}</TableCell>
                        </TableRow>
                    ))}
                    </TableBody>
                </Table>
            </TableContainer>
        )
    }

    return (
        <Container component="main" maxWidth="lg">
            <CssBaseline />
            <div className={classes.paper}>
            <Grid container spacing={2}>
                <Grid item xs={12} md={8}>
                    {renderProductsTable()}
                </Grid>
                <Grid item xs={12} md={4}>
                    <Typography component="h1" variant="h5">
                    Add Product
                    </Typography>
                    <form className={classes.form} noValidate onSubmit={submitProduct}>
                    <Grid container spacing={2}>
                        <Grid item xs={12}>
                            <Input
                                name="name"
                                variant="outlined"
                                required
                                fullWidth
                                id="productName"
                                label="Product Name"
                                placeholder="Product Name"
                                autoFocus
                                handleChange={(e) => setProductData({...productData, name: e.target.value})}
                            />
                        </Grid>                    
                        <Grid item xs={12}>
                            <FormControl variant="outlined" className={classes.formControl}>
                                <InputLabel htmlFor="outlined-category-native-simple">Category</InputLabel>
                                <Select
                                    native
                                    value={formData.category_id}
                                    onChange={(e) => setProductData({...productData, category_id: e.target.value})}
                                    label="Category"
                                    inputProps={{
                                        name: 'category',
                                        id: 'outlined-category-native-simple',
                                    }}
                                >
                                <option aria-label="None" value="" />
                                {categories.map((category) => (
                                    <option key={category.id} value={category.id}>{category.name}</option>
                                ))}
                                </Select>
                            </FormControl>
                        </Grid>
                        <Grid item xs={12}>
                            <Input
                                variant="outlined"
                                required
                                fullWidth
                                name="price"
                                label="Price"
                                placeholder="Price"
                                id="price"
                                handleChange={(e) => setProductData({...productData, price: e.target.value})}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <Input
                                variant="outlined"
                                required
                                fullWidth
                                name="stock"
                                label="Stock"
                                placeholder="Stock"
                                id="stock"
                                handleChange={(e) => setProductData({...productData, stock: e.target.value})}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <Input
                                variant="outlined"
                                required
                                fullWidth
                                rows={5}
                                name="description"
                                label="Description"
                                placeholder="Description"
                                id="description"
                                multiline
                                handleChange={(e) => setProductData({...productData, description: e.target.value})}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <input type="file" onChange={(e) => setProductData({...productData, image: e.target.files[0]})} />
                        </Grid>
                    </Grid>
                    <Button
                        type="submit"
                        fullWidth
                        variant="contained"
                        color="primary"
                        className={classes.submit}
                    >
                        Submit
                    </Button>
                    </form>
                </Grid>
            </Grid>

            </div>
        </Container>
    )
}

export default AddProduct;