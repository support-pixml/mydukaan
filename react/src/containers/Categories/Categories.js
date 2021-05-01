import { Avatar, Button, Container, CssBaseline, FormControl, Grid, InputLabel, makeStyles, Paper, Table, TableBody, TableCell, TableContainer, TableHead, TableRow } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { Link, Redirect } from 'react-router-dom';
import Input from '../../components/UI/Input';
import {useDispatch, useSelector} from 'react-redux';
import { getCategories } from '../../actions/categories';

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

const ShowCategories = () => {
    const classes = useStyles();
    const dispatch = useDispatch();
    var formData = new FormData();

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    const categories = useSelector((state) => state.category.categories);
    console.log(categories);

    const submitProduct = (e) => {
        e.preventDefault();
        // formData.append('name', productData.name);
        // formData.append('image', productData.image);
        // dispatch(addProduct(formData));
    }

    const renderCategoriesTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Category Name</TableCell>
                        <TableCell>Image</TableCell>
                        <TableCell align="right">Action</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {categories.map((category, index) => {
                        return (
                        <TableRow key={index}>
                            <TableCell component="td" scope="row">
                                {category.name}
                            </TableCell>
                            <TableCell component="td" scope="row">
                                <Avatar alt={category.name} variant="rounded" src={`/uploads/categories/${category.image}`} />
                            </TableCell>
                        </TableRow>
                        )
                    })}
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
                <Grid item xs={12} md={12}>
                    {renderCategoriesTable()}
                </Grid>                
            </Grid>
            </div>
        </Container>
    )
}

export default ShowCategories;