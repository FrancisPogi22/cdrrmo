<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Evacuee Data</title>
</head>

<body>
    <table class="table">
        <thead>
            <tr>
                <th>DATE AND <br>TIME OF ENTRY</th>
                <th>HH#</th>
                <th>No.</th>
                <th>NAME</th>
                <th>SEX</th>
                <th>Age</th>
                <th>4Ps?</th>
                <th>PWD</th>
                <th>PREGNANT</th>
                <th>LACTATING</th>
                <th>STUDENT</th>
                <th>WORKING</th>
                <th>DATE AND TIME <br>RETURNED HOME</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($evacueeData as $data)
                <tr>
                    <td data-column="Date Entry">{{ $data->date_entry }}</td>
                    <td data-column="HH#">{{ $data->house_hold_number }}</td>
                    <td data-column="No.">{{ $data->id }}</td>
                    <td data-column="Name">{{ $data->full_name }}</td>
                    <td data-column="Sex">{{ $data->sex }}</td>
                    <td data-column="Age">{{ $data->age }}</td>
                    <td data-column="fourps">{{ $data->fourps }}</td>
                    <td data-column="PWD">{{ $data->PWD }}</td>
                    <td data-column="Pregnant">{{ $data->pregnant }}</td>
                    <td data-column="Lactating">{{ $data->lactating }}</td>
                    <td data-column="Student">{{ $data->student }}</td>
                    <td data-column="Working">{{ $data->working }}</td>
                    <td data-column="Date Out">{{ $data->date_out }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
