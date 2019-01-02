Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-page',
            path: '/nova-page',
            component: require('./components/Tool'),
        },
    ])
})
