//
// Created by zhangzhikun on 2019/9/10.
//

#include "snow.h"

unsigned long long SnowFlake::last_timestamp = 0;
unsigned int SnowFlake::sequence = 0;

SnowFlake::SnowFlake() : _sequence_max(1), _work_id(1) {
    std::bitset<_sequence_bits> seq_bits;
    seq_bits.set();
    _sequence_max = seq_bits.to_ullong();
}

uint64_t SnowFlake::current_time_msc() {
    timeval tv{};
    gettimeofday(&tv, nullptr);
    return ((unsigned long long) tv.tv_sec * 1000 + (unsigned long long) tv.tv_usec / 1000);
}

uint64_t SnowFlake::next_time_msc(uint64_t last) {
    auto _now = current_time_msc();
    while (_now <= last) {
        _now = current_time_msc();
    }
    return _now;
}

void SnowFlake::__construct(Php::Parameters &params) {
    if (!params[0].isNumeric()) {
        throw Php::Exception("Not a numeric type.");
    }
    std::bitset<_worker_id_bits> work_bits;
    work_bits.set();
    auto work_max = work_bits.to_ulong();
    int64_t p1 = params[0];
    if (p1 < 1 || p1 > static_cast<int64_t>(work_max)) {
        std::ostringstream msg;
        msg << "work_id must be (1," << work_max << "].";
        throw Php::Exception(msg.str());
    }
    _work_id = params[0];
}

Php::Value SnowFlake::gen_id() {
    auto timestamp = current_time_msc();
    auto lastTimestamp = last_timestamp;
    std::ostringstream msg;
    if (timestamp < lastTimestamp) {
        msg << "Clock moved backwards.  Refusing to generate id for" << lastTimestamp - timestamp << "milliseconds";
        throw Php::Exception(msg.str());
    }
    //生成唯一序列
    if (lastTimestamp == timestamp) {
        //同一毫秒内4096个id
        ++sequence;
        if (sequence >= _sequence_max) {
            //这里多进程（线程）会有问题，建议加锁
            timestamp = next_time_msc(lastTimestamp);
        }
    } else {
        sequence = 0;
    }
    last_timestamp = timestamp;
    //
    auto timestampLeftShift = _sequence_bits + _worker_id_bits;
    auto workerIdShift = _sequence_bits;
    std::bitset<64> t(timestamp - _start_time);
    std::bitset<64> w(static_cast<uint64_t> (_work_id));
    std::bitset<64> s(static_cast<uint64_t> (sequence));
    t <<= timestampLeftShift;
    w <<= workerIdShift;
    auto ors = w | t | s;
    return static_cast<int64_t>(ors.to_ullong());
}