<table class="table">
    <thead>
        <tr>
            <th colspan="20" style="text-align: center;font-weight:bold;background-color:#4ca2ff;font-size:20mm">
                Assessment Question Result - <?php echo date('d-M-Y'); ?> </th>
        </tr>
        <tr>
            <th><b>Asessment Title</b></th>
            <th><b>Time to Complete</b></th>
            {{-- <th><b>Cadre</b></th>
            <th><b>State</b></th> --}}
            <th><b>Question</b></th>
            <th><b>Option1</b></th>
            <th><b>Option2</b></th>
            <th><b> Option3</b></th>
            <th><b> Option4</b></th>
            <th><b>Correct answer</b></th>
            <th><b>Assessment Date</b></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan='{{ count($assessment_question) + 1 }}'>{{ $assessment->assessment_title }}</td>
            <td rowspan='{{ count($assessment_question) + 1 }}'>{{ $assessment->time_to_complete }}</td>
        </tr>
        @foreach ($assessment_question as $item)
            <tr>
                <td>{{ $item->question }}</td>
                <td>{{ $item->option1 }}</td>
                <td>{{ $item->option2 }}</td>
                <td>{{ $item->option3 }}</td>
                <td>{{ $item->option4 }}</td>
                <td>{{ $item->correct_answer }}</td>
                <td>{{ $assessment->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
