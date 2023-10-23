<table class="table">
    <thead>
        <tr>
            <th colspan="20" style="text-align: center;font-weight:bold;background-color:#4ca2ff;font-size:20mm">Assessment Quiz Result - <?php echo date('d-M-Y'); ?> </th>
        </tr>
        <tr>
            <th><b>Asessment Title</b></th>
            <th><b>Name</b></th>
            <th><b>Cadre</b></th>
            <th><b>State</b></th>
            <th><b>District</b></th>
            <th><b>Block</b></th>
            <th><b>Health Facility</b></th>
            <th><b>Mobile No.</b></th>
            <th><b>Total Marks</b></th>
            <th><b>Obtained Marks</b></th>
            <th><b>Wrong Answer</b></th>
            <th><b>Skipped</b></th>
            <th><b>Assessment Submit Date</b></th>
            @foreach ($assessment_question as $questions)
                <th><b>{{ $questions->question }}</b></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($assessment_quiz_result as $item)
            <tr>
                <td>{{ $item->assessment_with_trashed->assessment_title }}</td>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->user->cadre->title }}</td>
                <td>
                    @if (isset($item->user->state))
                        {{ $item->user->state->title }}
                    @else
                        --
                    @endif
                </td>
                <td>
                    @if (isset($item->user->district))
                        {{ $item->user->district->title }}
                    @else
                        --
                    @endif
                </td>
                <td>
                    @if (isset($item->user->block))
                        {{ $item->user->block->title }}
                    @else
                        --
                    @endif
                </td>
                <td>
                    @if (isset($item->user->health_facility))
                        {{ $item->user->health_facility->health_facility_code }}
                    @else
                        --
                    @endif
                </td>
                <td>{{ $item->user->phone_no }}</td>

                <td>{{ $item->total_marks }}</td>
                <td>{{ $item->obtained_marks }}</td>
                <td>{{ $item->wrong_answers }}</td>
                <td>{{ $item->skipped }}</td>
                <td>{{ $item->created_at }}</td>

                @foreach ($item->assessment_user_quiz_answer as $option)
                    {{-- @foreach ($assessment_question as $question) --}}
                        @if ($item->user->id == $option->user_id && isset($option->assessment_question) && $option->assessment_question->id == $option->question_id)
                            @if($option->answer == null || $option->answer == 'null')
                                <td>skipped</td>
                            @else
                                <td>{{ $option->is_correct }}</td>
                            @endif
                        @else
                            @continue

                        @endif
                    {{-- @endforeach --}}

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
