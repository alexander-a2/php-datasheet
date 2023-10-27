How it works:

1. Create datasheet with `new Datasheet($source);`
2. Build datasheet with `DatasheetBuilder->build($datasheet)`
3. Render datasheet

Datasheet building steps:

1. Resolve data reader
2. Build columns
3. Attach datasheet filters
4. Attach column filters
5. Fill filters with requested params
6. Apply filters
7. Read data with applied filters
