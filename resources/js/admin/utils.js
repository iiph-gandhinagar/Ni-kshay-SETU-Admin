export const editiorConfig = {
    autogrow: true,
    removeformatPasted: true,
    imageWidthModalEdit: true,
    btnsDef: {
        image: {
            dropdown: ["insertImage", "upload", "base64"],
            ico: "insertImage"
        },
        align: {
            dropdown: [
                "justifyLeft",
                "justifyCenter",
                "justifyRight",
                "justifyFull"
            ],
            ico: "justifyLeft"
        }
    },
    // tagClasses: {
    //     h1: 'ABCD', // Bootstrap example
    //     blockquote: 'bg-grey-100 rounded-xl', // Tailwind CSS example
    // },
    btns: [
        ["formatting"],
        ["removeformat"],
        ["strong", "em", "del"],
        ["undo", "redo"],
        ["align"],
        ["unorderedList", "orderedList", "table"],
        ["foreColor", "backColor"],
        ["link", "image"],
        // ['link', 'noembed', 'image'],
        ["template"],
        ["fullscreen", "viewHTML"]
    ],
    plugins: {
        templates: [
            {
                name: "Normal Text",
                html: "<p>I am a Normal Text Content!</p>"
            },
            {
                name: "Unordered List",
                html:
                    "<p><strong>List Title</strong></p><ul><li>Hello 1</li><li>Hello 2</li><li>Hello 3</li></ul>"
            },
            {
                name: "Ordered List",
                html:
                    "<p><strong>List Title</strong></p><ol><li>Hello 1</li><li>Hello 2</li><li>Hello 3</li></ol>"
            },
            {
                name: "Note Template",
                html: "<em>I am a Note Text Content!</em>"
            },
            {
                name: "Blue Title and List",
                html:
                    '<span style="color:#30AAB9"><strong>Title</strong></span><ul><li>Item 1</li><li>Item 2</li></ul>'
            },
            {
                name: "Video",
                html:
                    '<iframe autoplay="false" style="align-self:center; width:100%;" allowfullscreen="" src="https://api.nikshay-setu.in/media/74/nHbFaY8fXxWO4NatbGaG9FWbtbbKGuD0mXC6QSDX.mp4"></iframe>'
            },
            {
                name: "Cascaded List",
                html: `<p><strong><span style="color:#1abc9c">Health worker level (Treatment supporter, ASHA, Community member)</span></strong></p>
                        <ul>
                            <li>Counsel patients &amp; reassure them that &nbsp;these symptoms resolve with time</li>
                            <li>Remind patients
                                <ul>
                                    <li>To do not take all the drugs together</li>
                                    <li>To take the drugs &nbsp;with little water</li>
                                    <li>To take the drugs embedded in banana or with milk or at bedtime</li>
                                    <li>To take a light meal (biscuits, bread, rice) before consuming the drugs</li>
                                    <li>Sub center level (CHO, ANM &amp; MPHW)<br></li>
                                </ul>
                            </li>
                        </ul>`
            },
            {
                name: "Table",
                html: `<table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th style="text-align:left">Column 1</th>
                                <th style="text-align:left">Column 2</th>
                                <th style="text-align:left">Column 3</th>
                                <th style="text-align:left">Column 4</th>
                                <th style="text-align:left">Column 5</th>
                                <th style="text-align:left">Column 6</th>
                                <th style="text-align:left">Column 7</th>
                                <th style="text-align:left">Column 8</th>
                                <th style="text-align:left">Column 9</th>
                                <th style="text-align:left">Column 10</th>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                            <tr>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                                <td style="text-align:left">Val 1</td>
                                <td style="text-align:left">Val 2</td>
                            </tr>
                        </tbody>
                    </table>`
            },
            {
                name: "Common Table List",
                html: `<table class="table table-bordered align-middle">
                        <tbody>
                        <tr>
                            <th>sr no</th>
                            <th>sr no</th>
                            <th>sr no</th>
                            <th>sr no</th>
                        </tr>
                        <tr class="lightyellow">
                            <td rowspan="5">Val 1</td>
                            <td rowspan="5">Val 1</td>
                            <td>Val 1</td>
                            <td>Val 1</td>       
                        </tr>

                        <tr class="lightyellow">
                            <td>Val 1</td>
                            <td>Val 1</td>       
                        </tr>
                        <tr class="lightyellow">
                            <td>Val 1</td>
                            <td>Val 1</td>       
                        </tr>
                        <tr class="lightyellow">
                            <td>Val 1</td>
                            <td>Val 1</td>       
                        </tr>
                    </tbody>
                    </table>`
            },
            {
                name: "Common One RowSpan Table List",
                html:`<table class="table table-bordered align-middle">
                            <tbody>
                            <tr>
                                <th>sr no</th>
                                <th>sr no</th>
                                <th>sr no</th>
                                <th>sr no</th>
                            </tr>
                            <tr class="lightyellow">
                                <td rowspan="5">Val 1</td><td>Val 1</td>
                                <td>Val 1</td>
                                <td>Val 1</td>       
                            </tr>

                            <tr class="lightyellow">
                                <td>Val 1</td>
                                <td>Val 1</td>
                                <td>Val 1</td>       
                            </tr>
                            <tr class="lightyellow">
                            <td>Val 1</td>
                                <td>Val 1</td>
                                <td>Val 1</td>       
                            </tr>
                            <tr class="lightyellow">
                            <td>Val 1</td>
                                <td>Val 1</td>
                                <td>Val 1</td>       
                            </tr>
                        </tbody>
                    </table>`
            },
            {
                name: "Master Table List",
                html:` <table class="table table-bordered align-middle">
                        <tbody><tr class="yellow">
                            <td colspan="1">1</td>
                            <td colspan="2">2</td>
                            <td colspan="2">3</td>
                        </tr>
                        <tr class="h-double lightblue">
                            <td colspan="2" rowspan="2">4</td>
                            <td colspan="3">5</td>
                        </tr>
                        <tr class="h-double lightgreen">
                            <td colspan="2">6</td>
                            <td rowspan="3">7</td>
                        </tr>
                        <tr class="lightyellow">
                            <td rowspan="5">8</td>
                            <td>9</td>
                            <td>10</td>            
                            <td rowspan="2">11</td>            
                        </tr>
                        <tr class="salmon">
                            <td colspan="2">12</td>
                        </tr>
                        <tr class="pink">
                            <td rowspan="3">13</td>
                            <td colspan="3">14</td>
                        </tr>
                        <tr class="tomato">
                            <td rowspan="1" colspan="2">15</td>
                            <td rowspan="1" colspan="1">16</td>
                        </tr>
                        <tr class="gold">
                            <td rowspan="1" colspan="3">17</td>
                        </tr>
                    </tbody></table>`
            },
             {
                name: "Collspan Table List",
                html:`<table class="table table-bordered align-middle">
                        <tbody>
                        <tr>
                            <th>sr no</th>
                            <th>sr no</th>
                            <th>sr no</th>
                            <th>sr no</th>
                        </tr>
                        <tr class="lightyellow">
                            <td colspan="4">Val 1</td>
                                
                        </tr>

                        <tr class="lightyellow">
                            <td colspan="3">Val 1</td>
                            <td>Val 1</td> 
                        </tr>
                        <tr class="lightyellow">
                            <td colspan="2">Val 1</td>
                            <td>Val 1</td>       
                            <td>Val 1</td>       
                        </tr>
                        <tr class="lightyellow">
                            <td>Val 1</td>
                            <td>Val 1</td>       
                            <td>Val 1</td>       
                            <td>Val 1</td>       
                        </tr>
                        <tr class="lightyellow">
                            <td>Val 1</td>
                            <td>Val 1</td>
                            <td>Val 1</td>       
                            <td>Val 1</td>       
                        </tr>
                    </tbody>
                </table>`
            }
        ]
    }
};
